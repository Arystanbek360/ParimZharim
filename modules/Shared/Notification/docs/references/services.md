## interface PushNotificationService

```php
public static function sendNotification(notification: Notification): bool
```

## class FirebasePushNotificationService implements PushNotificationService

```php
public static function sendNotification(notification: Notification): bool
```


## NotificationService extends BaseDomainService

```php
    public function createNotificationForUser(user: User, notification: Notification): Notification

    if user is null, then fill user_to_notification for all users that have device token
    else create notification for user
```
