<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum Roles: string implements BaseEnum
{
    use BaseEnumTrait;

    case SUPER_ADMIN = 'Super admin';
    case ADMIN = 'Admin';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Супер администратор',
            self::ADMIN => 'Пользователь административной панели',
        };
    }
}
