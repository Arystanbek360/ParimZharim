<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Models\OrderCollection;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryOrdersByCustomer extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderCustomerID): OrderCollection
    {
        return $this->orderRepository->getOrdersByCustomerId($orderCustomerID);
    }
}
