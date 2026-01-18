<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum OrderPermission
 *
 * Перечисление разрешений для работы с заказами.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = OrderPermission::VIEW_ORDERS;
 * echo $permission->label(); // Выведет: "Просмотр заказов".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-07
 */
enum OrderPermission: string implements BaseEnum
{
    use BaseEnumTrait;
    case VIEW_ORDERS = 'View orders';
    case MANAGE_ORDERS = 'Manage orders';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_ORDERS => 'Просмотр заказов',
            self::MANAGE_ORDERS => 'Управление заказами',
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
            self::VIEW_ORDERS => 'Просмотр списка и информации заказов и настроек бронирования, включая тарифы и расписание',
            self::MANAGE_ORDERS => 'Создание, редактирование и управление заказами и бронированием, применение скидок, синхронизация с iiko',
        };
    }
}
