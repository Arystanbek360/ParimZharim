<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum OrderSource: string implements BaseEnum
{
    use BaseEnumTrait;

    case MOBILE_APP = 'MOBILE_APP';
    case ADMIN_PANEL = 'ADMIN_PANEL';

    public function label(): string
    {
        return match ($this) {
            self::MOBILE_APP => 'Моб.приложение',
            self::ADMIN_PANEL => 'Админ. панель',

        };
    }
}
