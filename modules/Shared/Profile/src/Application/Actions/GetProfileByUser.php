<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;

class GetProfileByUser extends BaseAction {

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}
    public function handle(User $user): ?Profile
    {
        return $this->profileRepository->getProfileByUser($user);
    }
}
