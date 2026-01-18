<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Repositories;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTierCollection;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface LoyaltyProgramCustomerRepository extends BaseRepositoryInterface
{
    public function getLoyaltyProgramCustomerById(int $customerId): ?LoyaltyProgramCustomer;

    public function getAvailableDiscountTiersForCustomer(int $currentDiscount, float $customerOrderTotal): DiscountTierCollection;

    public function getApplicableDiscountForTotalOrderAmount(float $totalOrderAmount): ?int;

    public function save(LoyaltyProgramCustomer $customer): void;
}
