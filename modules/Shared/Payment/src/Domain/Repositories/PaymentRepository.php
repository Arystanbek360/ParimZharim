<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentItemCollection;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;

interface PaymentRepository extends BaseRepositoryInterface {

    public function getPaymentById(int $id): Payment;

    public function getPaymentsForOrder(int $orderId): PaymentCollection;

    public function getPaymentsByStatus(PaymentStatus $status): PaymentCollection;

    public function savePayment(Payment $payment, ?PaymentItemCollection $paymentItems = null): void;

    public function getPaymentStatusForPayment(Payment $payment): PaymentStatus;

    public function findPaymentsByOrderAndStatus(int $orderId, array $statuses, ?PaymentMethodType $paymentMethodType = null): ?PaymentCollection;
}
