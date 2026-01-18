<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum NotificationPermission
 *
 * Перечисление разрешений для работы с уведомлениями.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = NotificationPermission::VIEW_NOTIFICATIONS;
 * echo $permission->label(); // Выведет: "Просмотр уведомлений".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-27
 */
enum NotificationPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_NOTIFICATIONS = 'View notifications';
    case MANAGE_NOTIFICATIONS = 'Manage notifications';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_NOTIFICATIONS => 'Просмотр уведомлений',
            self::MANAGE_NOTIFICATIONS => 'Управление уведомлениями',
        };
    }

    /**
     * Возвращает отображаемое описание для разрешения.
     *
     * @return string
     */
    public function description(): string
    {
        return match ($this) {
            self::VIEW_NOTIFICATIONS => 'Просмотр уведомлений в системе',
            self::MANAGE_NOTIFICATIONS => 'Создание и управление уведомлениями',
        };
    }
}
