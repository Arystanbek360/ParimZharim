<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum ContentPermission
 *
 * Перечисление разрешений для работы с контентом.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = ContentPermission::VIEW_CONTENT;
 * echo $permission->label(); // Выведет: "Просмотр контента страницы".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-27
 */
enum ContentPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_CONTENT = 'View content';
    case MANAGE_CONTENT = 'Manage content';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_CONTENT => 'Просмотр контента',
            self::MANAGE_CONTENT => 'Управление контентом',
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
            self::VIEW_CONTENT => 'Просмотр контента страницы',
            self::MANAGE_CONTENT => 'Создание, редактирование, удаление, восстановление и копирование контента страницы',
        };
    }
}
