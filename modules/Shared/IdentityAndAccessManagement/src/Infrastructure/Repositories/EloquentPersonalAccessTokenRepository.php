<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories;

use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;

class EloquentPersonalAccessTokenRepository extends BaseRepository implements PersonalAccessTokenRepository {
    public function deleteByName(User $user, string $device_id): void
    {
        $user->tokens()->where('name', $device_id)->delete();
    }

    public function deleteCurrentUserToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function deleteAllUserTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}
