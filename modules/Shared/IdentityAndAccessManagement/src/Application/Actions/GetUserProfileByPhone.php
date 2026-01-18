<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class GetUserProfileByPhone extends BaseAction {

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function handle(string $phone): ?User {
        return $this->userRepository->findByPhone($phone);
    }

}
