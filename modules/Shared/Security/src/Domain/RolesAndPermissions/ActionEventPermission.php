<?php declare(strict_types=1);

namespace Modules\Shared\Security\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum ActionEventPermission
 *
 * Перечисление разрешений для просмотра подробной информации о событиях действий в системе.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = ActionEventPermission::VIEW_ACTION_EVENT;
 * echo $permission->label(); // Выведет: "Просмотр события"
 *
 * @see Modules\Shared\Core\Domain\BaseEnum
 *
 * @version 1.0.0
 * @since 2024-06-13
 */
enum ActionEventPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_ACTION_EVENT = 'View action Event';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_ACTION_EVENT => 'Просмотр события',
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
            self::VIEW_ACTION_EVENT => 'Просмотр информации о событиях действий в системе (лог действий)'
        };
    }
}
