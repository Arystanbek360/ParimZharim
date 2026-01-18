<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\OrderItem;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum OrderItemType: string implements BaseEnum
{
    use BaseEnumTrait;
    case PRODUCT = 'PRODUCT';
    case SERVICE = 'SERVICE';
    case SERVICE_OBJECT = 'SERVICE_OBJECT';


    public function label(): string
    {
        return match ($this) {
            self::PRODUCT => 'Меню',
            self::SERVICE => 'Сервис',
            self::SERVICE_OBJECT => 'Объект',
        };
    }
}
