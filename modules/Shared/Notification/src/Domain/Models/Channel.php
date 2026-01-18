<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

/**
 * Перечисление Channel
 *
 * Определяет каналы отправки уведомлений. Каждое значение перечисления
 * соответствует определённому каналу, через который может быть отправлено уведомление.
 *
 * **Каналы:**
 * - `EMAIL` — Отправка уведомления по электронной почте.
 * - `PUSH` — Отправка уведомления через push-уведомления.
 * - `SMS` — Отправка уведомления через SMS.
 * - `WHATSAPP` — Отправка уведомления через WhatsApp.
 * - `TELEGRAM` — Отправка уведомления через Telegram.
 *
 * @property string $value Значение канала отправки уведомлений.
 *
 * Пример использования:
 * $channel = Channel::EMAIL;
 *
 * @see BaseEnum
 *
 * @version 1.0.0
 * @since 2024-10-07
 */
enum Channel: string implements BaseEnum
{
    use BaseEnumTrait;

    /**
     * Отправка уведомления по электронной почте.
     */
   // case EMAIL = 'email';

    /**
     * Отправка уведомления через push-уведомления.
     */
    case PUSH = 'push';

    /**
     * Отправка уведомления через SMS.
     */
  //  case SMS = 'sms';

    /**
     * Отправка уведомления через WhatsApp.
     */
  //  case WHATSAPP = 'whatsapp';

    /**
     * Отправка уведомления через Telegram.
     */
 //   case TELEGRAM = 'telegram';

    /**
     * Получить отображаемое наименование для канала отправки уведомления.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
          //  self::EMAIL => 'Электронная почта',
            self::PUSH => 'Push-уведомление',
         //   self::SMS => 'SMS',
         //   self::WHATSAPP => 'WhatsApp',
         //   self::TELEGRAM => 'Telegram',
        };
    }
}
