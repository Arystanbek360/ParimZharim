<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\Notification\Domain\Models\NotifiableUserDevice;
use Modules\Shared\Notification\Domain\Repositories\NotifiableUserDeviceRepository;

class GetNotifiableUserDeviceByUserAndDeviceId extends BaseAction
{

    public function __construct(
        private readonly NotifiableUserDeviceRepository $userDeviceRepository
    ) {}

    public function handle(int $userId, string $deviceId): ?NotifiableUserDevice
    {
        return $this->userDeviceRepository->getUserDeviceByUserIdAndDeviceId($userId, $deviceId);
    }
}
