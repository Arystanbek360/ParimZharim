<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Random\RandomException;

class RequestPhoneChangePhoneVerificationCodeForUser extends BaseAction {
    public function __construct(
        private readonly UserRepository                  $userRepository
    ) {}

    /**
     * @throws InvalidInputData
     * @throws RandomException
     * @throws PhoneVerificationCodeRateLimitError
     */
    public function handle(User $user, string $phone): void {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($phone);

        // find existing user with new phone
        $this->validateThatPhoneIsNotAlreadyInUse($phone);

        // send phone verification code
        SendPhoneVerificationCodeForUser::make()->handle($user, $phone);
    }

    /**
     * @throws InvalidInputData
     */
    private function validateThatPhoneIsNotAlreadyInUse(string $phone): void {
        if ($this->userRepository->findByPhone($phone)) {
            throw new InvalidInputData("Phone number already exists");
        }
    }
}
