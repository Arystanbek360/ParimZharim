<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Application\Actions;

use Modules\ParimZharim\LoyaltyProgram\Domain\Errors\LoyaltyProgramCustomerNotFound;
use Modules\ParimZharim\LoyaltyProgram\Domain\Services\LoyaltyProgramService;
use Modules\Shared\Core\Application\BaseAction;

class RecalculateLoyaltyProgramCustomerDiscount extends BaseAction
{

    /**
     * @throws LoyaltyProgramCustomerNotFound
     */
    public function handle($customerId): void
    {
        $customer = GetLoyaltyProgramCustomerById::make()->handle($customerId);
        if (!$customer) {
            throw new LoyaltyProgramCustomerNotFound($customerId);
        }
        LoyaltyProgramService::recalculateDiscountForCustomer($customer);
    }
}
