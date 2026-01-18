<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class GetCustomerByUser extends BaseAction {

    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}
    public function handle(User $user): ?Customer
    {
        return $this->customerRepository->getCustomerByUser($user);
    }
}
