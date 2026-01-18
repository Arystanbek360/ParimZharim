<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Models;

use Modules\Shared\IdentityAndAccessManagement\Domain\Models\UserDevice;

/**
 * Класс NotifiableUserDevice *(Устройства для отправки уведомлений)*
 *
 * Основная сущность, представляющая устройства, на которые можно отправлять уведомления.
 *
 * @property string $device_token Токен устройства.
 *
 * @version 1.0.0
 * @since 2024-10-07
 *
 */
class NotifiableUserDevice extends UserDevice
{
    protected $table = 'idm_user_devices';

    protected $fillable = [
        'user_id',
        'device_id',
        'device_token',
    ];
}
