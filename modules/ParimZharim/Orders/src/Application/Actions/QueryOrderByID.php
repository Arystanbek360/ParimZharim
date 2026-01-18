<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryOrderByID extends BaseAction {


    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    /**
     * @throws OrderNotFound
     */
    public function handle(int $orderID): ?Order
    {
        $order =  $this->orderRepository->getOrderById($orderID);
        if (!$order) {
            throw new OrderNotFound($orderID);
        }

        return $order;
    }
}
