<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceObjectOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\ParimZharim\Orders\Infrastructure\Errors\OrderItemWasNotSaved;
use Modules\ParimZharim\Orders\Infrastructure\Errors\OrderWasNotSaved;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Throwable;

class EloquentOrderRepository extends BaseRepository implements OrderRepository
{

    public function saveOrder(Order $order): void
    {
        try {
            DB::beginTransaction();
            foreach ($order->orderItems as $orderItem) {
                if (!$orderItem->save()) {
                    throw new OrderItemWasNotSaved();
                }
            }
            $order->orderItems()->saveMany($order->orderItems);
            if (!$order->save()) {
                throw new OrderWasNotSaved();
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function saveOrderQuietly(Order $order): void
    {
        try {
            DB::beginTransaction();
            foreach ($order->orderItems as $orderItem) {
                if (!$orderItem->saveQuietly()) {
                    throw new OrderItemWasNotSaved();
                }
            }
            $order->orderItems()->saveMany($order->orderItems);
            if (!$order->saveQuietly()) {
                throw new OrderWasNotSaved();
            }
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws OrderItemWasNotSaved
     * @throws Throwable
     * @throws OrderWasNotSaved
     */
    public function addOrderItems(Order $order, OrderItemCollection $orderItemCollection): void
    {
        foreach ($orderItemCollection as $orderItem) {
            $orderItem->save();
            $order->orderItems->add($orderItem);
        }
        $this->saveOrder($order);
    }

    public function getOrderById(int $orderId): ?Order
    {
        return Order::find($orderId);
    }

    public function getOrdersByCustomerId(int $customerId): OrderCollection
    {
        $orders = Order::where('customer_id', $customerId)
            ->orderBy('start_time', 'desc')
            ->get();

        return new OrderCollection($orders->all());
    }

    public function getActiveOrderByCustomer(int $customerId): ?Order
    {
        //where statuses are not OrderStatus::CANCELLED or OrderStatus::COMPLETED
        $orders = Order::where('customer_id', $customerId)
            ->whereNotIn('status', [OrderStatus::CANCELLED, OrderStatus::COMPLETED, OrderStatus::FINISHED])
            ->get();
        return $orders->first();
    }

    /**
     * @throws OrderItemWasNotSaved
     * @throws Throwable
     * @throws OrderWasNotSaved
     */
    public function changeOrderStatus(int $orderId, OrderStatus $status): void
    {
        $order = $this->getOrderById($orderId);
        $order->status = $status;
        $this->saveOrder($order);
    }

    public function getOrdersByStatus(OrderStatus $status): OrderCollection
    {
        $orders = Order::where('status', $status)->get();
        return new OrderCollection($orders->all());
    }

    /**
     * @throws Throwable
     */
    public function createOrderableServiceObjectOrderItemIfNotExist(Order $order): void
    {
        if ($order->orderItems->count() == 0) {
            try {
                DB::beginTransaction();
                // Create a new OrderableServiceObjectOrderItem and set its attributes.
                $orderItem = new OrderableServiceObjectOrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->orderable_id = $order->orderable_service_object_id;
                $orderItem->quantity = 1;
                $orderItem->type = OrderItemType::SERVICE_OBJECT;

                OrderService::calculateOrderItemsPrice($orderItem);
                $maxOrderItemHourPrice = $orderItem->calculateAdvancePayment();
                $metadata = $order->metadata;
                $metadata['expectedAdvancePayment'] = $maxOrderItemHourPrice;
                $order->metadata = $metadata;
                $orderItem->save();
                $order->orderItems()->save($orderItem);
                $order->save();
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        }
    }

    public function getOrdersToNotify(): OrderCollection
    {
        $orders = Order::where('status', OrderStatus::CONFIRMED)
            ->where('start_time', '=', now()->addDay()->startOfMinute())
            ->get();

        return new OrderCollection($orders);
    }


    public function getCompletedOrdersByCustomerId(int $customerId): OrderCollection
    {
        $orders = Order::where('customer_id', $customerId)
            ->orderBy('start_time', 'desc')
            ->where('status', OrderStatus::COMPLETED)
            ->get();

        return new OrderCollection($orders->all());
    }
}
