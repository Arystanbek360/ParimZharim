<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum ProfilePermission
 *
 * Перечисление разрешений для работы с профилями в системе.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = ProfilePermission::VIEW_CUSTOMER_PROFILES;
 * echo $permission->label(); // Выведет: "Просмотр профилей клиентов".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-04-22
 */
enum ProfilePermission: string implements BaseEnum
{
    use BaseEnumTrait;
    case VIEW_CUSTOMER_PROFILES = 'View customer profiles';
    case VIEW_EMPLOYEE_PROFILES = 'View employee profiles';
    case MANAGE_CUSTOMER_PROFILES = 'Manage customer profiles';
    case MANAGE_EMPLOYEE_PROFILES = 'Manage employee profiles';

    case MANAGE_EMPLOYEE_ROLES = 'Manage employee roles';

    case CHANGE_EMPLOYEE_PASSWORD = 'Change employee password';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_CUSTOMER_PROFILES => 'Просмотр профилей клиентов',
            self::VIEW_EMPLOYEE_PROFILES => 'Просмотр профилей сотрудников',
            self::MANAGE_CUSTOMER_PROFILES => 'Управление профилями клиентов',
            self::MANAGE_EMPLOYEE_PROFILES => 'Управление профилями сотрудников',
            self::MANAGE_EMPLOYEE_ROLES => 'Управление ролями сотрудников',
            self::CHANGE_EMPLOYEE_PASSWORD => 'Изменение пароля сотрудника',
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
            self::VIEW_CUSTOMER_PROFILES => 'Просмотр списка и информации клиентов',
            self::VIEW_EMPLOYEE_PROFILES => 'Просмотр списка и информации сотрудников',
            self::MANAGE_CUSTOMER_PROFILES => 'Создание профиля клиента, редактирование и удаление его данных',
            self::MANAGE_EMPLOYEE_PROFILES => 'Создание профиля сотрудника, редактирование, удаление и восстановление его данных',
            self::MANAGE_EMPLOYEE_ROLES => 'Назначение и снятие ролей сотрудникам',
            self::CHANGE_EMPLOYEE_PASSWORD => 'Изменение пароля сотрудника',
        };
    }
}
