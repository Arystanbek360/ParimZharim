<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ChangePhoneForUserByVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Application\ApplicationErrors\InvalidInputData;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;
use Throwable;


class ChangePhoneNumberForUserAndProfile extends BaseAction
{

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}

    /**
     * @throws Throwable
     */
    public function handle(User $user, string $phone, int $code): void
    {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($phone);

        $customer = GetProfileByUser::make()->handle($user);

        if (!$customer) {
            throw new InvalidInputData("Profile not found");
        }

        // Find existing customer with phone
        $existingCustomer = GetProfileByPhone::make()->handle($phone);
        if ($existingCustomer) {
            throw new InvalidInputData("Phone number already exists");
        }

        // Change the phone
        $customer->phone = $phone;

        try {
            DB::beginTransaction();
            ChangePhoneForUserByVerificationCode::make()->handle(new PhoneChangeRequestData($user, $phone, $code));
            $this->profileRepository->saveProfile($customer);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
