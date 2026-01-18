<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories;

use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;

class EloquentUserDeviceRepository extends BaseRepository implements UserDeviceRepository
{


    public function deleteAllUserDevices(User $user): void
    {
        $user->devices()->forceDelete();
    }

    public function deleteUserDevice(User $user, string $device_id): void
    {
        $user->devices()->where('device_id', $device_id)->forceDelete();
    }

    public function deleteCurrentUserDevice(User $user): void
    {
        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            // Используем отношение tokens() для нахождения текущего токена в базе
            $device_id = $currentToken->name;

            $user->devices()->where('device_id', $device_id)->forceDelete();
        }
    }

}
