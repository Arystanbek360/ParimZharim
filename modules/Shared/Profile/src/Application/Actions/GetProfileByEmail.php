<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;

class GetProfileByEmail extends BaseAction {

    public function __construct(
        private readonly ProfileRepository $profileRepository
    )
    {}

    public function handle(string $email): ?Profile
    {
        return $this->profileRepository->getProfileByEmail($email);
    }
}
