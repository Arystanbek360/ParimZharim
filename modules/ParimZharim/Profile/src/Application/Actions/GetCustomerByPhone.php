<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Application\BaseAction;

class GetCustomerByPhone extends BaseAction {

    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}

    public function handle(string $phone): ?Customer
    {
        return $this->customerRepository->getCustomerByPhone($phone);
    }
}
