<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum RolePermission
 *
 * Перечисление разрешений для работы с ролями в системе.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = RolePermission::Просмотр ролей();
 * echo $permission->label(); // Выводит: "Просмотр ролей"
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-04-18
 */
enum RolePermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_ROLES = 'View roles';
    case MANAGE_ROLES = 'Manage roles';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_ROLES => 'Просмотр ролей',
            self::MANAGE_ROLES => 'Управление ролями'
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
            self::VIEW_ROLES => 'Просмотр списка доступных ролей в системе',
            self::MANAGE_ROLES => 'Создание, редактирование информации и прикрепленных разрешений и удаление ролей (кроме Админа)'
        };
    }
}
