<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

interface PersonalAccessTokenRepository extends BaseRepositoryInterface {

    public function deleteByName(User $user, string $device_id): void;

    public function deleteCurrentUserToken(User $user): void;

    public function deleteAllUserTokens(User $user): void;
}
