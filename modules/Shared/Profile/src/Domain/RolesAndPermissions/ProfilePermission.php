<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\RolesAndPermissions;

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
 * $permission = ProfilePermission::VIEW_PROFILES();
 * echo $permission->label();
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-02
 */
enum ProfilePermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_PROFILES = 'View profiles';
    case MANAGE_PROFILES = 'Manage profiles';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_PROFILES => 'View profiles',
            self::MANAGE_PROFILES => 'Manage profiles',
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
            self::VIEW_PROFILES => 'Просмотр профилей',
            self::MANAGE_PROFILES => 'Управление профилями',
        };
    }
}
