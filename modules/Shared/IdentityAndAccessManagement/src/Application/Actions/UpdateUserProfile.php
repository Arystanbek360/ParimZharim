<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class UpdateUserProfile extends BaseAction {

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function handle(User $user, UserProfileData $data): void {
        // Update user profile
        $user->name = $data->name ?? $user->name;
        if ($data->password) {
            $user->password = $data->password;
        }
        $this->userRepository->save($user);
    }

}
