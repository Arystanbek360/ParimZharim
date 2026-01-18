<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\Notification\Domain\Models\NotifiableUserDevice;

interface NotifiableUserDeviceRepository extends BaseRepositoryInterface
{

    public function getUserDeviceByUserIdAndDeviceId(int $userId, string $deviceId): ?NotifiableUserDevice;

    public function save(NotifiableUserDevice $userDevice): void;

    public function cleanDeviceToken(int $userId, string $deviceId): void;
}
