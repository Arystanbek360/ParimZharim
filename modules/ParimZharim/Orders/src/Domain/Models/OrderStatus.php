<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum OrderStatus: string implements BaseEnum
{
    use BaseEnumTrait;

    case CREATED = 'CREATED';
    case CONFIRMED = 'CONFIRMED';
    case STARTED = 'STARTED';
    case FINISHED = 'FINISHED';
    case COMPLETED = 'COMPLETED';
    case CANCELLED = 'CANCELLED';

    case CANCELLATION_REQUESTED = 'CANCELLATION_REQUESTED';


    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Создан',
            self::CONFIRMED => 'Подтвержден',
            self::STARTED => 'Начат',
            self::FINISHED => 'Время истекло',
            self::COMPLETED => 'Выполнен',
            self::CANCELLED => 'Отменен',
            self::CANCELLATION_REQUESTED => 'Запрошена отмена',
        };
    }
}
