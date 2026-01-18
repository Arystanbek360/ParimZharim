<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Application;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Domain\Models\NotifiableUserDevice;
use Modules\Shared\Notification\Domain\Repositories\NotifiableUserDeviceRepository;

class UpdateDeviceToken extends BaseAction
{
    public function __construct(
        private readonly NotifiableUserDeviceRepository $userDeviceRepository,
    ) {}

    public function handle(User $user, string $deviceId, string $deviceToken): void
    {
        $userDevice = GetNotifiableUserDeviceByUserAndDeviceId::make()->handle($user->id, $deviceId);

        if ($userDevice) {
            $userDevice->device_token = $deviceToken;
        } else {
            $userDevice = new NotifiableUserDevice([
                'user_id'      => $user->id,
                'device_id'    => $deviceId,
                'device_token' => $deviceToken,
            ]);
        }
        $this->userDeviceRepository->save($userDevice);
    }
}
