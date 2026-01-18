<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Application\NotifyAllAdmins;

class ConfirmOrder extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);

        if (!in_array($order->status, [OrderStatus::CREATED, OrderStatus::CANCELLED, OrderStatus::CANCELLATION_REQUESTED])) {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::CONFIRMED,
                currentStatus: $order->status);
        }

        if ($order->status == OrderStatus::CANCELLED->value) {
            NotifyAllAdmins::make()->handle("Предоплачен отмененный заказ: " . $order->id);
        }

        $this->orderRepository->changeOrderStatus($orderID, OrderStatus::CONFIRMED);
    }
}
