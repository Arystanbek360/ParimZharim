<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Перечисление `AccessMode`
 * Возможные значения для режима доступа к документам и пакетам документов.
 *
 * @property string $value Значение режима доступа.
 *
 * @example
 * $type = AccessMode::ANY_USER;
 * echo $type->label(); // Вывод: "Все пользователи"
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
enum AccessMode: string implements BaseEnum
{
    use BaseEnumTrait;

    /**
     * Режим доступа **"Все пользователи"**
     * Описывает режим доступа, при котором доступ есть у всех пользователей.
     */
    case ANY_USER = 'AnyUser';

    /**
     * Режим доступа **"Определенные пользователи"**
     * Описывает режим доступа, при котором доступ есть у конкретных пользователей.
     */
    case SPECIFIC_USERS = 'SpecificUsers';

    /**
     * Получить отображаемое наименование для режима доступа.
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ANY_USER => 'Все пользователи',
            self::SPECIFIC_USERS => 'Конкретные пользователи',
        };
    }
}
