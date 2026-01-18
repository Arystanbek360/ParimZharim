## SendNotification
Класс `SendNotification` предназначен для отправки уведомлений. 
Вызывает Action отправки уведомлений для каждого пользователя.

**Пример кода:**

```php
class SendNotification implements ShouldQueue, ShouldBeUnique
{
    public function handle()
    {
        SendAllNotSentNotifications::dispatch();
    }
}
 
