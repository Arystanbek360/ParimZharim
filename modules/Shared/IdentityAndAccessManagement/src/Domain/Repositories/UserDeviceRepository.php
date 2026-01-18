<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

interface UserDeviceRepository extends BaseRepositoryInterface {

    public function deleteAllUserDevices(User $user): void;

    public function deleteUserDevice(User $user, string $device_id): void;

    public function deleteCurrentUserDevice(User $user): void;
}
