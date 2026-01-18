<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\Shared\Core\Application\BaseAction;

class QueryActiveOrderForCustomer extends BaseAction {

    public function __construct(
        private readonly OrderRepository $orderRepository
    )
    {}

    public function handle(int $orderCustomerID): ?Order
    {
        return $this->orderRepository->getActiveOrderByCustomer($orderCustomerID);
    }
}
