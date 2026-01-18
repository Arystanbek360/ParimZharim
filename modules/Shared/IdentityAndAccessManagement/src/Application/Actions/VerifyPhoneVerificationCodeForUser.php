<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;

class VerifyPhoneVerificationCodeForUser extends BaseAction {

    public function __construct(
        private readonly PhoneVerificationCodeRepository $phoneVerificationCodeRepository
    ) {}

    /**
     * @throws AuthenticationError
     */
    public function handle(User $user, string $phone, string $code): void {
        $phoneVerificationCode = $this->phoneVerificationCodeRepository->findLastAndActiveForUserAndPhone($user, $phone);

        if (!$phoneVerificationCode || $phoneVerificationCode->code != $code) {
            throw new AuthenticationError("Invalid code");
        }

        $this->phoneVerificationCodeRepository->markAsExpired($phoneVerificationCode);
    }
}
