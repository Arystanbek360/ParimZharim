<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Services;

use Modules\Shared\Core\Domain\BaseServiceInterface;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;


interface PaymentService extends BaseServiceInterface {

    public function getPaymentData(Payment $payment): array;
    public function getPaymentForm(Payment $payment): ?string;

    public function cancel(Payment $payment): void;

    public function syncFromPaymentSystemProvider(int $paymentID): Payment;

    public function bindPaymentToExternalSystem(Payment $payment, mixed $externalSystemData): void;
    public function saveExternalSystemTransactionLog(Payment $payment, mixed $externalSystemData): void;

    public function completePayment(int $paymentID): void;
}
