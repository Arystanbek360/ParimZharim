<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Repositories;

use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\OrderCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemCollection;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface OrderRepository extends BaseRepositoryInterface {

    //both for customer and admin
    public function saveOrder(Order $order): void;
    public function saveOrderQuietly(Order $order): void;

    public function addOrderItems(Order $order, OrderItemCollection $orderItemCollection): void;

    public function getOrderById(int $orderId): ?Order;

    public function getOrdersByStatus(OrderStatus $status): OrderCollection;

    public function getOrdersByCustomerId(int $customerId): OrderCollection;

    public function getCompletedOrdersByCustomerId(int $customerId): OrderCollection;

    public function getActiveOrderByCustomer(int $customerId): ?Order;

    // for confirm, start, finish, cancel, complete order
    public function changeOrderStatus(int $orderId, OrderStatus $status): void;

    public function getOrdersToNotify(): OrderCollection;
}
