<?php declare(strict_types=1);
namespace Modules\Shared\Notification\Infrastructure\Repositories;

use Illuminate\Support\Facades\Log;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\Notification\Domain\Models\NotifiableUserDevice;
use Modules\Shared\Notification\Domain\Repositories\NotifiableUserDeviceRepository;

class EloquentNotifiableUserDeviceRepository extends  BaseRepository implements NotifiableUserDeviceRepository
{

    public function getUserDeviceByUserIdAndDeviceId(int $userId, string $deviceId): ?NotifiableUserDevice
    {
        return NotifiableUserDevice::where('user_id', $userId)->where('device_id', $deviceId)->first();
    }

    public function save(NotifiableUserDevice $userDevice): void
    {
        $userDevice->save();
    }

    public function cleanDeviceToken(int $userId, string $deviceId): void
    {
        $userDevice = NotifiableUserDevice::where('user_id', $userId)->where('device_id', $deviceId)->first();
        if ($userDevice) {
            $userDevice->device_token = null;
            $userDevice->save();
        }
    }

}
