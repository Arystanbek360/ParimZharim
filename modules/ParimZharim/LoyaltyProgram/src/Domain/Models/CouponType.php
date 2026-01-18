<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;


enum CouponType: string implements BaseEnum
{
    use BaseEnumTrait;

    case MONEY = 'MONEY';
    case PERCENT = 'PERCENT';
    case BONUS = 'BONUS';
    case MULTIPLIER = 'MULTIPLIER';


    public function label(): string
    {
        return match ($this) {
            self::MONEY => 'Деньги',
            self::PERCENT => '%',
            self::BONUS => 'Бонус',
            self::MULTIPLIER => 'Множитель',
        };
    }
}
