<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Application\DTO\CustomerProfileData;
use Modules\Shared\Core\Application\BaseAction;
use Throwable;


class UpdateCustomer extends BaseAction
{


    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}
    /**
     * @throws Throwable
     */
    public function handle(Customer $customer, CustomerProfileData $data): void
    {
        $customer->name = $data->name ?? $customer->name;
        $customer->date_of_birth = $data->dateOfBirth ?? $customer->date_of_birth;
        $customer->email = $data->email ?? $customer->email;

        $this->customerRepository->saveCustomer($customer);
    }
}
