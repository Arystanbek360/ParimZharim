<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Modules\ParimZharim\Profile\Application\ApplicationError\InvalidRegistrationData;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\ParimZharim\Profile\Application\DTO\CustomerProfileData;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;


class CreateCustomerForUserIfNotExists extends BaseAction
{
    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}
    /**
     * @throws Throwable
     */
    public function handle(CustomerProfileData $data, User $user): void
    {
        if (!$data->phone) {
            throw new InvalidRegistrationData('Phone are required');
        }

        $customer = GetCustomerByPhone::make()->handle($data->phone);
        if ($customer) {
            return;
        }

        $customer = new Customer();

        $customer->name = $data->name ?? $data->phone;
        $customer->phone = $data->phone;
        $customer->email = $data->email;
        $customer->date_of_birth = $data->dateOfBirth;

        $this->customerRepository->saveCustomerAndAssociateWithUser($customer, $user);
    }
}
