<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Payment\Domain\Events\PaymentFailed;
use Modules\Shared\Payment\Domain\Events\PaymentSucceeded;
use Modules\Shared\Payment\Domain\Models\Payment;
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
    public function syncFromPaymentSystemProvider(int $paymentID): Payment
    {
        $response = $this->auth()->post($this->base_url . 'v2/payments/find', [
            'InvoiceID' => $paymentID
        ]);

        $data = $response->json();
        $payment = $this->paymentRepository->getPaymentById($paymentID);

        if (isset($data['Model'])) {
            $payment->external_id = $data['Model']['TransactionId'];
            $payment->status = $this->mapStatus($data['Model']['Status']);
            $payment->total = $data['Model']['Amount'];
            $payment->comment = $data['Model']['Description'];
            $payment->customer_id = $data['Model']['AccountId'];
            $errorReason = $data['Model']['Reason'] ?? null;
            $this->paymentRepository->savePayment($payment);

            match ($payment->status) {
                PaymentStatus::SUCCESS => PaymentSucceeded::dispatch($payment),
                PaymentStatus::FAILED => PaymentFailed::dispatch($payment, $errorReason),
                default => null,
            };
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
}
