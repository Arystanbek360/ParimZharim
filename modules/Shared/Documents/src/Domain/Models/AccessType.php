<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Перечисление `AccessType`
 * Возможные значения для типов доступа к документам и пакетам документов.
 *
 * @property string $value Значение типа доступа.
 *
 * @example
 * $type = AccessType::READ;
 * echo $type->label(); // Вывод: "Чтение"
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
enum AccessType: string implements BaseEnum
{
    use BaseEnumTrait;

    /**
     * Тип доступа **"Чтение"**
     * Описывает тип доступа, при котором возможно только чтение документов.
     */
    case READ = 'READ';

    /**
     * Тип доступа **"Комментирование"**
     * Описывает тип доступа, при котором возможно комментирование документов.
     */
    case COMMENT = 'COMMENT';

    /**
     * Тип доступа **"Запись"**
     * Описывает тип доступа, при котором возможна запись/перезапись документов.
     */
    case WRITE = 'WRITE';

    /**
     * Возвращает уровень доступа в количественном значении.
     * @return int
     */
    public function getLevel(): int
    {
        return match ($this) {
            self::READ => 0,
            self::COMMENT => 1,
            self::WRITE => 2
        };
    }

    /**
     * Получить отображаемое наименование для типа доступа.
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::READ => 'Чтение',
            self::COMMENT => 'Комментирование',
            self::WRITE => 'Запись',
        };
    }
}
