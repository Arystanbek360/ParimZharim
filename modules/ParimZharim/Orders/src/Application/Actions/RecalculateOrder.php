<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Errors\OrderNotFound;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderRepository;
use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\Shared\Core\Application\BaseAction;

class RecalculateOrder extends BaseAction
{

    public function handle(int $orderID): void
    {

        $order = QueryOrderByID::make()->handle($orderID);
        OrderService::recalculateOrder($order);
    }
}
