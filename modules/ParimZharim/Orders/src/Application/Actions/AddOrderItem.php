<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class AddOrderItem extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderID, array $orderItemData): void
    {
        $order = QueryOrderByID::make()->handle($orderID); // Get order by ID

        $orderItems = []; // Array to store new order items

        foreach ($orderItemData as $item) {
            if ($item->type === 'service') {
                $orderItem = new OrderableServiceOrderItem([
                    'order_id' => $order->id,
                    'orderable_id' => $item->orderableID,
                    'quantity' => $item->quantity,
                    'type' => OrderItemType::SERVICE
                ]);
            } else { // Assuming 'product' as the only other type
                $orderItem = new OrderableProductOrderItem([
                    'order_id' => $order->id,
                    'orderable_id' => $item->orderableID,
                    'quantity' => $item->quantity,
                    'type' => OrderItemType::PRODUCT,
                ]);
            }
            $orderItems[] = $orderItem; // Add to array
        }

        $orderItemCollection = new OrderItemCollection($orderItems); // Create collection from array
        $this->orderRepository->addOrderItems($order, $orderItemCollection); // Add to order
    }
}