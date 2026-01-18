<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Domain\RolesAndPermissions;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum Roles: string implements BaseEnum
{
    use BaseEnumTrait;

    case TEMPLATE = 'TEMPLATE';

    public function label(): string
    {
        return match ($this) {
            self::TEMPLATE => 'Шаблон роли',
        };
    }
}
