<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;

class GetProfileByPhone extends BaseAction {

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}

    public function handle(string $phone): ?Profile
    {
        return $this->profileRepository->getProfileByPhone($phone);
    }
}
