<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum PermissionPermission
 *
 * Перечисление разрешений для работы с разрешениями.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = PermissionPermission::VIEW_PERMISSIONS;
 * echo $permission->label(); // Выведет: "Просмотр разрешений".
 *
 * @see Modules\Shared\Core\Domain\BaseEnum
 *
 * @version 1.0.0
 * @since 2024-04-18
 */
enum PermissionPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_PERMISSIONS = 'View permissions';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_PERMISSIONS => 'Просмотр разрешений'
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
            self::VIEW_PERMISSIONS => 'Просмотр списка доступных разрешений (прав доступа) в системе',
        };
    }
}
