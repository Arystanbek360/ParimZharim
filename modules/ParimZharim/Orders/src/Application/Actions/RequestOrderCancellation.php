<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Application\NotifyAllAdmins;
use Throwable;

class RequestOrderCancellation extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    /**
     * @throws OrderNotFound
     * @throws StatusChangeViolation|Throwable
     */
    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);
        if ($order->status == OrderStatus::CREATED) {
            CancelOrder::make()->handle($orderID);
        } else if ($order->status == OrderStatus::CONFIRMED) {
            $this->orderRepository->changeOrderStatus($orderID, OrderStatus::CANCELLATION_REQUESTED);
            NotifyAllAdmins::make()->handle("Запрошена отмена заказа {$orderID}");
        } else {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::CANCELLATION_REQUESTED,
                currentStatus: $order->status);
        }

    }
}
