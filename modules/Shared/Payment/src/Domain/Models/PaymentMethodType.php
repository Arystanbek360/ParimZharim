<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum PaymentMethodType: string implements BaseEnum
{
    use BaseEnumTrait;

    case CASH = 'Cash';
    case KASPI = 'Kaspi';
    case CLOUD_PAYMENT = 'CloudPayment';
    case ApplePay = 'Apple Pay';

    public function label(): string
    {
        return match ($this) {
            //labels on russian
            self::CASH => 'Наличные',
            self::KASPI => 'Перевод Kaspi',
            self::CLOUD_PAYMENT => 'Банковская карта',
            self::ApplePay => 'Apple Pay',
        };
    }
}
