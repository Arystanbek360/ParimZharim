<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class ChangePhoneForUserByVerificationCode extends BaseAction {

    public function __construct(
        private readonly UserRepository                  $userRepository,
    ) {}

    /**
     * @throws InvalidInputData
     * @throws AuthenticationError
     */
    public function handle(PhoneChangeRequestData $data): void {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($data->phone);

        // Find existing user with phone
        $existingUser = $this->userRepository->findByPhone($data->phone);
        if ($existingUser) {
            throw new InvalidInputData("Phone number already exists");
        }

        // Check if the code is correct
        VerifyPhoneVerificationCodeForUser::make()->handle($data->user, $data->phone, (string) $data->code);

        // Change the phone
        $data->user->phone = $data->phone;
        $this->userRepository->save($data->user);
    }
}
