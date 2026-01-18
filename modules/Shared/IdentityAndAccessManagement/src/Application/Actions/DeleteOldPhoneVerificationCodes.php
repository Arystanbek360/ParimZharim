<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;

class DeleteOldPhoneVerificationCodes extends BaseAction{
    public function __construct(
        private readonly PhoneVerificationCodeRepository $phoneVerificationCodeRepository
    ) {}

    public function handle(int $days = 60): void
    {
        $this->phoneVerificationCodeRepository->deleteOlderThan($days);
    }
}
