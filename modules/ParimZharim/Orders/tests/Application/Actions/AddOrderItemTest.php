<?php

declare(strict_types=1);

namespace Modules\ParimZharim\Tests\Unit\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Application\Actions\AddOrderItem;
use Modules\ParimZharim\Orders\Application\Actions\QueryOrderByID;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemType;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableProductOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderableServiceOrderItem;
use Modules\ParimZharim\Orders\Domain\Models\OrderItem\OrderItemCollection;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\ParimZharim\Orders\Tests\TestCase;
use Mockery;
use Illuminate\Container\Container;

class AddOrderItemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHandleAddsOrderItems(): void
    {
        $container = Container::getInstance();

        $orderID = 1;
        $order = Mockery::mock(Order::class);
        $order->shouldReceive('getAttribute')->with('id')->andReturn($orderID);

        // Данные для тестирования
        $orderItemData = [
            (object)[
                'orderableID' => 101,
                'quantity' => 2,
                'type' => 'product'
            ],
            (object)[
                'orderableID' => 202,
                'quantity' => 3,
                'type' => 'service'
            ],
        ];

        // Моки для элементов заказа
        $orderItems = [
            new OrderableProductOrderItem([
                'order_id' => $orderID,
                'orderable_id' => 101,
                'quantity' => 2,
                'type' => OrderItemType::PRODUCT,
            ]),
            new OrderableServiceOrderItem([
                'order_id' => $orderID,
                'orderable_id' => 202,
                'quantity' => 3,
                'type' => OrderItemType::SERVICE,
            ]),
        ];

        $orderItemCollection = new OrderItemCollection($orderItems);
        $orderRepository = Mockery::mock(OrderRepository::class);
        $orderRepository->shouldReceive('getOrderById')
            ->with($orderID)
            ->andReturn($order);
        $orderRepository->shouldReceive('addOrderItems')
            ->once()
            ->with($order, Mockery::on(function ($collection) use ($orderItemCollection) {
                return $collection->toArray() == $orderItemCollection->toArray();
            }));

        // Подмена экземпляра репозитория в контейнере приложения
        $container->instance(OrderRepository::class, $orderRepository);

        // Мок действия QueryOrderByID
        $queryOrderByID = Mockery::mock(QueryOrderByID::class);
        $queryOrderByID->shouldReceive('handle')
            ->with($orderID)
            ->andReturn($order);

        // Подмена экземпляра QueryOrderByID в контейнере приложения
        $container->instance(QueryOrderByID::class, $queryOrderByID);

        // Выполнение действия
        $action = new AddOrderItem($orderRepository);
        $action->handle($orderID, $orderItemData);

        // Утверждения
        $this->assertTrue(true); // Проверка, что тест выполняется
        $this->assertEquals($orderID, $order->id); // Проверка корректности ID заказа
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
