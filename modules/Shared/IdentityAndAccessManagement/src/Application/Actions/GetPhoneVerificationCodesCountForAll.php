<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;

class GetPhoneVerificationCodesCountForAll extends BaseAction
{

    public function __construct(
        private readonly PhoneVerificationCodeRepository $repository
    )
    {
    }

    public function handle(): int
    {
        return $this->repository->countAllPerDay();
    }
}
