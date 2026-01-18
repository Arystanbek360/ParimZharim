### SendAllNotSentNotifications

В данном Action в зависимости от каналов нотификации происходит вызов сервисов, которые отправляют уведомления. То есть,
например, если в нотификации в каналах два или три различных канала, то для каждого уведомления вызываются два или три
различных метода отправки из сервисов. При этом перед этим получаются все уведомления, которые еще не отправлены для
юзеров.


```php

public function handle()
{
    $notifications = $this->notificationRepository->getNotSentNotifications();

    foreach ($notifications as $notification) {
        $notificationChannels = $notification->channels;

        foreach ($notificationChannels as $notificationChannel) {
            $channel = $notificationChannel->channel;
            $channelService = $this->getChanelService($channel);
            $channelService->send($notification);
        }
    }
}

```

### GetUnreadNotificationCountForUser 

Данный Action возвращает количество непрочитанных уведомлений для пользователя. Для этого используется метод
`getUnreadNotificationsCount` из репозитория уведомлений.

```php

public function handle(int $userId)
{
    return $this->notificationRepository->getUnreadNotificationsCount($userId);
}

```


### GetNotificationListForUser

Данный Action возвращает список уведомлений для пользователя. Для этого используется метод `getNotifications` из репозитория
уведомлений.

```php

public function handle(int $userId)
{
    return $this->notificationRepository->getNotifications($userId);
}

```

### MarkNotificationAsRead

Данный Action помечает уведомление как прочитанное. Для этого используется метод `markAsRead` из репозитория уведомлений.

```php

public function handle(int $notificationId)
{
    return $this->notificationRepository->markAsRead($notificationId);
}

```

