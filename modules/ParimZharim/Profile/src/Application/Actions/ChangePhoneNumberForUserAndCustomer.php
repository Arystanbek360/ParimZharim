<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Profile\Application\ApplicationError\InvalidInputData;
use Modules\ParimZharim\Profile\Domain\RepositoryInterfaces\CustomerRepository;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ChangePhoneForUserByVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;


class ChangePhoneNumberForUserAndCustomer extends BaseAction
{

    public function __construct(
        private readonly CustomerRepository $customerRepository
    )
    {}
    /**
     * @throws Throwable
     */
    public function handle(User $user, string $phone, int $code): void
    {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($phone);

        $customer = GetCustomerByUser::make()->handle($user);

        if (!$customer) {
            throw new InvalidInputData("Customer not found");
        }

        // Find existing customer with phone
        $existingCustomer = GetCustomerByPhone::make()->handle($phone);
        if ($existingCustomer) {
            throw new InvalidInputData("Phone number already exists");
        }

        // Change the phone
        $customer->phone = $phone;

        try {
            DB::beginTransaction();
            ChangePhoneForUserByVerificationCode::make()->handle(new PhoneChangeRequestData($user, $phone, $code));
            $this->customerRepository->saveCustomer($customer);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
