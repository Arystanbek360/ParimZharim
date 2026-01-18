<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Services\OrderService;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerById;
use Modules\Shared\Core\Application\BaseAction;

class GetTotalOrdersAmountForCustomer extends BaseAction
{
    public function handle(int $customerId): float
    {

        $customer = GetCustomerById::make()->handle($customerId);

        if ($customer === null) {
            return 0;
        }

        return OrderService::getCustomerTotalOrdersAmount($customerId);
    }
}
