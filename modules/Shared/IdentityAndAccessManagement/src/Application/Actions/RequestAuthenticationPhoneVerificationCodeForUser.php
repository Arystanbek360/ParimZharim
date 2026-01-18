<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Random\RandomException;

class RequestAuthenticationPhoneVerificationCodeForUser extends BaseAction {
    /**
     * @throws InvalidInputData
     * @throws RandomException
     * @throws PhoneVerificationCodeRateLimitError
     */
    public function handle(User $user, string $phone): void {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($phone);

        // send phone verification code
        SendPhoneVerificationCodeForUser::make()->handle($user, $phone);
    }

}
