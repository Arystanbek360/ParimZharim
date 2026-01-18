<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum ObjectPermission
 *
 * Перечисление разрешений для работы с объектами.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = ObjectPermission::VIEW_OBJECTS;
 * echo $permission->label(); // Выведет: "Просмотр объектов".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-04-22
 */
enum ObjectPermission: string implements BaseEnum
{
    use BaseEnumTrait;
    case VIEW_OBJECTS = 'View objects';
    case MANAGE_OBJECTS = 'Manage objects';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_OBJECTS => 'Просмотр объектов',
            self::MANAGE_OBJECTS => 'Управление объектами',
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
            self::VIEW_OBJECTS => 'Просмотр списка и информации объектов обслуживания, их категорий и тегов',
            self::MANAGE_OBJECTS => 'Создание объектов обслуживания, редактирование, удаление, восстановление и создание копий их информации, включая Теги и Категории',
        };
    }

}
