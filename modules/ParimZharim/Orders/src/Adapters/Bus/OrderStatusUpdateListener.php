<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Bus;

use Modules\ParimZharim\Orders\Application\Actions\ConfirmOrder;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\Shared\Core\Adapters\Bus\BaseListener;
use Modules\Shared\Core\Domain\BaseEvent;
use Modules\Shared\Notification\Application\NotifyAllAdmins;
use Modules\Shared\Payment\Domain\Events\PaymentFailed;
use Modules\Shared\Payment\Domain\Events\PaymentSucceeded;
use Modules\Shared\Payment\Domain\Models\Payment;

class OrderStatusUpdateListener extends BaseListener
{
    /**
     * @throws OrderNotFound
     */
    public function handle(BaseEvent $event): void
    {
        if ($event instanceof PaymentSucceeded) {
            $this->processSuccessfulPayment($event->payment);
        } elseif ($event instanceof PaymentFailed) {
            $this->processFailedPayment($event->payment, $event->reason);
        }
    }

    /**
     * @throws StatusChangeViolation
     */
    private function processSuccessfulPayment($payment): void
    {
        /** @var Payment $payment*/
        $orderID = $payment->payable_order_id;
        ConfirmOrder::make()->handle($orderID);
    }

    private function processFailedPayment($payment, $reason): void
    {
        NotifyAllAdmins::make()->handle("Оплата заказа {$payment->order_id} не прошла. Причина: $reason");
    }
}
