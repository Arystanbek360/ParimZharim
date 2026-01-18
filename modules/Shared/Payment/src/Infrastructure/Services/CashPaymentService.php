<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Services;

use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Payment\Domain\Events\PaymentFailed;
use Modules\Shared\Payment\Domain\Events\PaymentSucceeded;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Domain\Services\PaymentService;
use Modules\Shared\Payment\Infrastructure\Errors\FailedToCancelPayment;
use Throwable;


class CashPaymentService extends BaseService implements PaymentService
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository
    ){}

    public function syncFromPaymentSystemProvider(int $paymentID): Payment
    {
        $payment =  $this->paymentRepository->getPaymentById($paymentID);
        if ($payment->status === PaymentStatus::CREATED && ($payment->payment_method === PaymentMethodType::CASH || $payment->payment_method === PaymentMethodType::KASPI)) {
            $payment->status = PaymentStatus::PENDING;
            $this->paymentRepository->savePayment($payment);
        }
        return $payment;
    }

    public function cancel(Payment $payment): void
    {
        if (in_array($payment->status, [PaymentStatus::PENDING, PaymentStatus::CREATED, PaymentStatus::SUCCESS])) {
                $payment->status = PaymentStatus::CANCELED;
                $this->paymentRepository->savePayment($payment);
            }
        else {
            throw new FailedToCancelPayment($payment->external_id);
        }
    }
    public function getPaymentData(Payment $payment): array
    {
        return [];
    }

    public function getPaymentForm(Payment $payment): ?string
    {
        return null;
    }

    public function bindPaymentToExternalSystem(Payment $payment, mixed $externalSystemData): void {
        $payment->external_id = 'cash'. $payment->id;
        $this->paymentRepository->savePayment($payment);
    }

    public function saveExternalSystemTransactionLog(Payment $payment, mixed $externalSystemData): void {}

    public function completePayment(int $paymentID): void
    {
        $payment = $this->paymentRepository->getPaymentById($paymentID);
        try {
            $payment->status = PaymentStatus::SUCCESS;
            $this->paymentRepository->savePayment($payment);
            PaymentSucceeded::dispatch($payment);
        }
        catch (Throwable $e) {
            $payment->status = PaymentStatus::FAILED;
            $this->paymentRepository->savePayment($payment);
            PaymentFailed::dispatch($payment, $e->getMessage());
        }

    }
}
