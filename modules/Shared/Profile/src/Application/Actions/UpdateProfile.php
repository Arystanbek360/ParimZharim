<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;


use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Profile\Application\DTO\ProfileData;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;
use Throwable;


class UpdateProfile extends BaseAction
{

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}
    /**
     * @throws Throwable
     */
    public function handle(Profile $profile, ProfileData $data): void
    {
        $profile->name = $data->name ?? $profile->name;
        $profile->email = $data->email ?? $profile->email;

        $this->profileRepository->saveProfile($profile);
    }
}
