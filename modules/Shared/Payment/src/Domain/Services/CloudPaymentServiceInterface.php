<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Services;

use Modules\Shared\Payment\Domain\Models\Payment;


interface CloudPaymentServiceInterface extends PaymentService {

    public function getPaymentData(Payment $payment): array;
    public function getPaymentForm(Payment $payment): ?string;

    public function cancel(Payment $payment): void;

    public function syncFromPaymentSystemProvider(int $paymentID): Payment;

    public function bindPaymentToExternalSystem(Payment $payment, mixed $externalSystemData): void;
    public function saveExternalSystemTransactionLog(Payment $payment, mixed $externalSystemData): void;

    public function completePayment(int $paymentID): void;
}
