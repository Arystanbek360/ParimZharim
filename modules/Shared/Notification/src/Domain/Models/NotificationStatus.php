<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Перечисление NotificationStatus
 *
 * Определяет возможные статусы, которые может иметь уведомление.
 * Каждое значение перечисления соответствует определённому состоянию уведомления.
 *
 * **Статусы:**
 * - `CREATED` — Уведомление создано.
 * - `SENT` — Уведомление отправлено.
 * - `READ` — Уведомление прочитано.
 * - `FAILED` — Ошибка при отправке уведомления.
 *
 * @property string $value Значение статуса уведомления.
 *
 * Пример использования:
 * $status = NotificationStatus::SENT;
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-10-07
 */
enum NotificationStatus: string implements BaseEnum
{
    use BaseEnumTrait;

    /**
     * Уведомление создано.
     */
    case CREATED = 'created';

    /**
     * Уведомление отправлено.
     */

    case SENT = 'sent';

    /**
     * Уведомление прочитано.
     */
    case READ = 'read';

    /**
     * Ошибка при отправке уведомления.
     */
    case FAILED = 'failed';

    /**
     * Получить отображаемое наименование для статуса уведомления.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Создано',
            self::SENT => 'Отправлено',
            self::READ => 'Прочитано',
            self::FAILED => 'Ошибка отправки',
        };
    }
}
