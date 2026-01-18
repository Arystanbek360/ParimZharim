<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum PaymentPermission
 *
 * Перечисление разрешений для работы с платежами.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = PaymentPermission::VIEW_PAYMENT;
 * echo $permission->label(); // Выведет: "Просмотр платежей".
 *
 * @see Modules\Shared\Core\Domain\BaseEnum
 *
 * @version 1.0.0
 * @since 2024-05-23
 */
enum PaymentPermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_PAYMENT = 'View Payment';
    case MANAGE_PAYMENT = 'Manage Payment';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_PAYMENT => 'Просмотр платежей',
            self::MANAGE_PAYMENT => 'Управление платежами',
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
            self::VIEW_PAYMENT => 'Просмотр списка и информации платежей',
            self::MANAGE_PAYMENT => 'Отмена и подтверждение платежа',
        };
    }
}
