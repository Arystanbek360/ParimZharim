<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Enum PhoneVerificationCodePermission
 *
 * Перечисление разрешений для работы с SMS-кодами верификации.
 *
 * @property string $value Значение разрешения.
 *
 * @example
 * $permission = PhoneVerificationCodePermission::VIEW_PHONE_VERIFICATION_CODES;
 * echo $permission->label(); // Выведет: "Просмотр SMS-кодов верификации".
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-08-12
 */
enum PhoneVerificationCodePermission: string implements BaseEnum
{
    use BaseEnumTrait;

    case VIEW_PHONE_VERIFICATION_CODES = 'View Phone Verification Codes';
    case MANAGE_PHONE_VERIFICATION_CODES = 'Manage Phone Verification Codes';

    /**
     * Возвращает отображаемое наименование для разрешения.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::VIEW_PHONE_VERIFICATION_CODES => 'Просмотр SMS-кодов верификации',
            self::MANAGE_PHONE_VERIFICATION_CODES => 'Управление SMS-кодами верификации'
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
            self::VIEW_PHONE_VERIFICATION_CODES => 'Просмотр списка и информации SMS-кодов верификации',
            self::MANAGE_PHONE_VERIFICATION_CODES => 'Очистка SMS-кодов верификации за период'
        };
    }
}
