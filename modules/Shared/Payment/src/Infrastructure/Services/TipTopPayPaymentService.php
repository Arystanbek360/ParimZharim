<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Payment\Domain\Events\PaymentFailed;
use Modules\Shared\Payment\Domain\Events\PaymentSucceeded;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCard;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Domain\Services\CloudPaymentServiceInterface;
use Modules\Shared\Payment\Infrastructure\Errors\FailedToCancelPayment;

class TipTopPayPaymentService extends BaseService implements CloudPaymentServiceInterface
{
    protected string $publicId;
    protected string $apiSecret;

    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        public string                      $base_url = "https://api.tiptoppay.kz/"
    ) {
        $this->publicId = config('app.payment_tiptoppay_public_id');
        $this->apiSecret = config('app.payment_tiptoppay_secret');
    }

    public function getPaymentData(Payment $payment): array
    {
        return [
            'publicId' => $this->publicId,
            'description' => $payment->comment,
            'amount' => $payment->total,
            'currency' => 'KZT',
            'invoiceId' => $payment->id,
            'accountId' => $payment->customer_id,
        ];
    }

    public function getPaymentForm(Payment $payment): ?string
    {
        return 'payment::components.tiptoppay-form';
    }

    /**
     * @throws ConnectionException
     * @throws FailedToCancelPayment
     */
    public function cancel(Payment $payment): void
    {
        if ($payment->status === PaymentStatus::CREATED) {
            $payment->status = PaymentStatus::CANCELED;
            $this->paymentRepository->savePayment($payment);
            return;
        }

        if (in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::SUCCESS])) {
            $maxAttempts = 5;

            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $response = $this->auth()->post($this->base_url . 'payments/void', [
                        'TransactionId' => $payment->external_id,
                    ]);

                    $data = $response->json();

                    if (!empty($data['Success']) && $data['Success'] === true) {
                        $payment->status = PaymentStatus::CANCELED;
                        $this->paymentRepository->savePayment($payment);
                        return;
                    }

                    throw new FailedToCancelPayment($payment->external_id);

                } catch (ConnectionException | FailedToCancelPayment $e) {
                    $this->incrementCancelAttemptInMetadata($payment, $attempt, $e->getMessage());
                }
            }

            $payment->status = PaymentStatus::CANCELED;
            $this->paymentRepository->savePayment($payment);
        }
    }

    /**
     * Увеличивает счётчик попыток отмены и сохраняет детальную информацию в metadata.
     *
     * @param Payment $payment
     * @param int     $attemptNumber
     * @param string  $errorMessage
     */
    private function incrementCancelAttemptInMetadata(Payment $payment, int $attemptNumber, string $errorMessage): void
    {
        $metadata = $payment->metadata ?? [];

        // Счётчик всех неудачных попыток
        $metadata['cancelAttemptsCount'] = ($metadata['cancelAttemptsCount'] ?? 0) + 1;

        // Лог конкретных попыток
        $metadata['cancelAttemptsLog'][] = [
            'attempt'   => $attemptNumber,
            'error'     => $errorMessage,
            'timestamp' => now()->toDateTimeString(),
        ];

        $payment->metadata = $metadata;
        $this->paymentRepository->savePayment($payment);
    }

    /**
     * @throws ConnectionException
     */
    public function completePayment(int $paymentID): void
    {
        $payment = $this->syncFromPaymentSystemProvider($paymentID);
        if ($payment->status === PaymentStatus::SUCCESS) {
            $response = $this->auth()->post($this->base_url . 'payments/confirm', [
                'TransactionId' =>  $payment->external_id,
                'Amount' => $payment->total
            ]);

            $data = $response->json();
            if ($data['Success']) {
                $payment->status = PaymentStatus::COMPLETED;
                $this->paymentRepository->savePayment($payment);
            }
        }
    }

    /**
     * @throws ConnectionException
     */
    public function payByToken(Payment $payment, PaymentCard $paymentCard): Payment
    {
        $response = $this->auth()->post($this->base_url . 'payments/tokens/auth', [
            'Amount' => $payment->total,
            'Currency' => 'KZT',
            'AccountId' => (string)$payment->customer_id,
            'Token' => $paymentCard->token,
            'InvoiceId' => (string)$payment->id,
            'Description' => $payment->comment,
            'TrInitiatorCode' => 1,
        ]);

        $data = $response->json();
        $this->saveExternalSystemTransactionLog($payment, $data);

        if (!empty($data['Model'])) {
            if ($this->isThreeDsRequired($data['Model'])) {
                $this->saveThreeDsChallenge($payment, $data['Model']);
                return $payment;
            }

            $status = $this->mapStatus($data['Model']['Status']);
            if ($status === PaymentStatus::FAILED) {
                PaymentFailed::dispatch($payment, $data['Model']['Reason'] ?? null);
                return $payment;
            }

            $this->syncPaymentFromProviderModel($payment, $data['Model']);
            return $payment;
        }

        if (($data['Success'] ?? false) !== true) {
            PaymentFailed::dispatch($payment, $data['Message'] ?? null);
        }

        return $payment;
    }

    /**
     * @throws ConnectionException
     */
    public function completeThreeDsPayment(Payment $payment, string $transactionId, string $paRes): Payment
    {
        $response = $this->auth()->post($this->base_url . 'payments/cards/post3ds', [
            'TransactionId' => (int)$transactionId,
            'PaRes' => $paRes,
        ]);

        $data = $response->json();
        $this->saveExternalSystemTransactionLog($payment, $data);

        if (!empty($data['Model'])) {
            $this->syncPaymentFromProviderModel($payment, $data['Model']);
            $this->clearThreeDsChallenge($payment);
            return $payment;
        }

        PaymentFailed::dispatch($payment, $data['Message'] ?? null);

        return $payment;
    }

    /**
     * @throws ConnectionException
     */
    public function getTokensForAccount(string $accountId): array
    {
        $tokens = [];
        $page = 1;

        do {
            $response = $this->auth()->post($this->base_url . 'payments/tokens/list', [
                'PageNumber' => $page,
            ]);

            $data = $response->json();
            $items = $data['Model'] ?? [];

            foreach ($items as $item) {
                if ((string)($item['AccountId'] ?? '') === $accountId && !empty($item['Token'])) {
                    $tokens[] = $item;
                }
            }

            $page++;
        } while (($data['Success'] ?? false) === true && count($items) > 0 && $page <= 20);

        return $tokens;
    }

    /**
     * @throws ConnectionException
     */
    public function syncFromPaymentSystemProvider(int $paymentID): Payment
    {
        $response = $this->auth()->post($this->base_url . 'v2/payments/find', [
            'InvoiceID' => $paymentID
        ]);

        $data = $response->json();
        $payment = $this->paymentRepository->getPaymentById($paymentID);

        if (isset($data['Model'])) {
            $this->syncPaymentFromProviderModel($payment, $data['Model']);
        }

        return $payment;
    }

    public function bindPaymentToExternalSystem(Payment $payment, mixed $externalSystemData): void
    {
        $payment->external_id = $externalSystemData['TransactionId'];
        $this->paymentRepository->savePayment($payment);
    }

    public function saveExternalSystemTransactionLog(Payment $payment, mixed $externalSystemData): void
    {
        $metadata = $payment->metadata ?? [];
        $logs = $metadata['externalSystemTransactionLogs'] ?? [];
        $logs[] = $externalSystemData;

        $metadata['externalSystemTransactionLogs'] = $logs;
        $payment->metadata = $metadata; // полная замена metadata

        $this->paymentRepository->savePayment($payment);
    }

    private function auth(): PendingRequest
    {
        return Http::withBasicAuth($this->publicId, $this->apiSecret);
    }

    private function mapStatus(string $status): PaymentStatus
    {
        return match ($status) {
            'AwaitingAuthentication' => PaymentStatus::PENDING,
            'Authorized' => PaymentStatus::SUCCESS,
            'Completed' => PaymentStatus::COMPLETED,
            'Declined' => PaymentStatus::FAILED,
            'Cancelled' => PaymentStatus::CANCELED,
            default => PaymentStatus::CREATED,
        };
    }

    private function isThreeDsRequired(array $model): bool
    {
        return !empty($model['AcsUrl']) && !empty($model['PaReq']) && !empty($model['TransactionId']);
    }

    private function saveThreeDsChallenge(Payment $payment, array $model): void
    {
        $metadata = $payment->metadata ?? [];
        $metadata['threeDs'] = [
            'transactionId' => (string)$model['TransactionId'],
            'paReq' => $model['PaReq'],
            'acsUrl' => $model['AcsUrl'],
            'createdAt' => now()->toDateTimeString(),
        ];

        $payment->external_id = (string)$model['TransactionId'];
        $payment->status = PaymentStatus::PENDING;
        $payment->metadata = $metadata;
        $this->paymentRepository->savePayment($payment);
    }

    private function clearThreeDsChallenge(Payment $payment): void
    {
        $metadata = $payment->metadata ?? [];
        unset($metadata['threeDs']);
        $payment->metadata = $metadata;
        $this->paymentRepository->savePayment($payment);
    }

    private function syncPaymentFromProviderModel(Payment $payment, array $model): void
    {
        $payment->external_id = $model['TransactionId'];
        $payment->status = $this->mapStatus($model['Status']);
        $payment->total = $model['Amount'];
        $payment->comment = $model['Description'];
        $payment->customer_id = $model['AccountId'];
        $errorReason = $model['Reason'] ?? null;
        $this->paymentRepository->savePayment($payment);

        match ($payment->status) {
            PaymentStatus::SUCCESS => PaymentSucceeded::dispatch($payment),
            PaymentStatus::COMPLETED => PaymentSucceeded::dispatch($payment),
            PaymentStatus::FAILED => PaymentFailed::dispatch($payment, $errorReason),
            default => null,
        };
    }
}
