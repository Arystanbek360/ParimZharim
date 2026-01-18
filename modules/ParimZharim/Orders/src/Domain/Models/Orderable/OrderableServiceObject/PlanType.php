<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;


enum PlanType: string implements BaseEnum
{
    use BaseEnumTrait;
    case FIXED = 'FIXED';
    case HOURLY = 'HOURLY';


    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Фиксированный',
            self::HOURLY => 'Почасовой',
        };
    }
}
