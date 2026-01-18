<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Application\Actions;

use Modules\ParimZharim\LoyaltyProgram\Domain\Errors\LoyaltyProgramCustomerNotFound;
use Modules\ParimZharim\LoyaltyProgram\Domain\Services\LoyaltyProgramService;
use Modules\Shared\Core\Application\BaseAction;

class GetCurrentAndNextCustomerDiscount extends BaseAction
{


    /**
     * @throws LoyaltyProgramCustomerNotFound
     */
    public function handle($customerId): array
    {
        $customer = GetLoyaltyProgramCustomerById::make()->handle($customerId);
        if (!$customer) {
            throw new LoyaltyProgramCustomerNotFound($customerId);
        }
        $discount =  LoyaltyProgramService::getCurrentAndNextCustomerDiscount($customer);
        if ($discount['next_discount'] === null) {
            return [
                'current_discount' => $discount['current_discount'],
                'next_discount_from' => null,
                'need_to_next_discount' => null,
                'next_discount' => null,
                'total_order_amount' => $discount['total_order_amount']
            ];
        }

        return $discount;
    }
}
