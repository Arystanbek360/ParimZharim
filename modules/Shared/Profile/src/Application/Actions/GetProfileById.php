<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Profile\Domain\Errors\ProfileNotFound;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;

class GetProfileById extends BaseAction {

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}
    public function handle(int $profileId): Profile
    {
        $profile =  $this->profileRepository->getProfileById($profileId);
        if (!$profile) {
            throw new ProfileNotFound($profileId);
        }
        return $profile;
    }
}
