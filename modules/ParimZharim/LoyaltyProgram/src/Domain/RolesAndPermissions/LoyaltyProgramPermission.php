<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum LoyaltyProgramPermission
 *
 * Перечисление разрешений для работы с программой лояльности.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = LoyaltyProgramPermission::VIEW_LOYALTY_PROGRAM;
 * echo $permission->label(); // Выведет: "Просмотр программы лояльности"
 *
 */
enum LoyaltyProgramPermission: string implements BaseEnum
{
    use BaseEnumTrait;
    case VIEW_LOYALTY_PROGRAM = 'View loyalty program';
    case MANAGE_LOYALTY_PROGRAM = 'Manage loyalty program';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_LOYALTY_PROGRAM => 'Просмотр программы лояльности',
            self::MANAGE_LOYALTY_PROGRAM => 'Управление программой лояльности',
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
            self::VIEW_LOYALTY_PROGRAM => 'Просмотр Участников Программы Лояльности',
            self::MANAGE_LOYALTY_PROGRAM => 'Создание, редактирование и удаление информации об Участниках Программы Лояльности',
        };
    }
}
