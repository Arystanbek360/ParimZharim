<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum PaymentStatus: string implements BaseEnum
{
    use BaseEnumTrait;

    case CREATED = 'Created';
    case PENDING = 'Pending';
    case SUCCESS = 'Success';
    case FAILED = 'Failed';

    case CANCELED = 'Canceled';

    case COMPLETED = 'Completed';

    public function label(): string
    {
        return match ($this) {
            //labels on russian
            self::CREATED => 'Создан',
            self::PENDING => 'В ожидании',
            self::SUCCESS => 'Успешно',
            self::FAILED => 'Неудача',
            self::CANCELED => 'Отменен',
            self::COMPLETED => 'Завершен',
        };
    }
}
