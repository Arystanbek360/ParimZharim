## SendNotificationCommand

Класс `SendNotificationCommand` вызывает задание `SendNotification` каждую минуту.

```php
class SendNotificationCommand extends Command
{
    protected $signature = 'send:notification';
    protected $description = 'Отправляет уведомления каждую минуту';

    public function handle()
    {
        SendNotification::dispatch();
    }
}
