<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Application\Actions;

use Modules\ParimZharim\LoyaltyProgram\Domain\Models\LoyaltyProgramCustomer;
use Modules\ParimZharim\LoyaltyProgram\Domain\Repositories\LoyaltyProgramCustomerRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetLoyaltyProgramCustomerById extends BaseAction
{

    public function __construct(
        private readonly LoyaltyProgramCustomerRepository $loyaltyProgramCustomerRepository
    )
    {}

    public function handle($customerId): ?LoyaltyProgramCustomer
    {
        return $this->loyaltyProgramCustomerRepository->getLoyaltyProgramCustomerById($customerId);
    }
}
