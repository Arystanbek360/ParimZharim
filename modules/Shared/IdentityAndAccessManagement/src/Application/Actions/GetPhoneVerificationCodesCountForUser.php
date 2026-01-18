<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class GetPhoneVerificationCodesCountForUser extends BaseAction
{

    public function __construct(
        private readonly PhoneVerificationCodeRepository $repository,
        private readonly UserRepository $userRepository
    )
    {
    }

    /**
     * @throws UserNotFound
     */
    public function handle(string $id): int
    {
        $user = $this->userRepository->findById($id);
        if(!$user) throw new UserNotFound("User not found");

        $phone = $user->phone;
        return $this->repository->countAllByPhonePerDay($phone);
    }
}
