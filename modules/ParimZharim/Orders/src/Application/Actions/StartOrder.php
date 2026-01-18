<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class StartOrder extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderID): void
    {
        $order = QueryOrderByID::make()->handle($orderID);

        // available only for CONFIRMED orders
        if ($order->status !== OrderStatus::CONFIRMED) {
            throw new StatusChangeViolation(
                wishStatus: OrderStatus::STARTED,
                currentStatus: $order->status
            );
        }

        $this->orderRepository->changeOrderStatus($orderID, OrderStatus::STARTED);
    }
}
