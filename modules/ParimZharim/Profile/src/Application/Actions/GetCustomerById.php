<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Application\BaseAction;

class GetCustomerById extends BaseAction {

    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}

    public function handle(int $customerId): ?Customer
    {
        return $this->customerRepository->getCustomerById($customerId);
    }
}
