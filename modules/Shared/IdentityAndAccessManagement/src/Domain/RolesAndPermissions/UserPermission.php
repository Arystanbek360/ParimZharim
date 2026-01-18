<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum UserPermission
 *
 * Перечисление разрешений для работы с пользователями системы.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = UserPermission::VIEW_USERS();
 * echo $permission->label(); // Выводит: "Просмотр пользователей"
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-02
 */
enum UserPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_USERS = 'View users';
    case MANAGE_USERS = 'Manage users';
    case MANAGE_USER_ROLES = 'Manage user roles';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_USERS => 'Просмотр пользователей',
            self::MANAGE_USERS => 'Управление пользователями',
            self::MANAGE_USER_ROLES => 'Управление ролями пользователей'
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
            self::VIEW_USERS => 'Просмотр списка и информации пользователей',
            self::MANAGE_USERS => 'Создание и редактирование информации пользователей',
            self::MANAGE_USER_ROLES => 'Прикрепление и открепление ролей для пользователя'
        };
    }
}
