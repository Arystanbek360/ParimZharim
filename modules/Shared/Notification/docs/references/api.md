## API Notification Module

API уведомлений предоставляет механизм управления оповещениями для пользователей. Система позволяет загружать
уведомления, помечать их прочитанными и отправлять на сервер информацию о токенах устройств.

### Общая схема работы:

- **Загрузка уведомлений**: При запросе списка уведомлений возвращается список с указанием состояния каждого
  уведомления (прочитано/непрочитано). Пользователь может управлять пагинацией списка.

- **Пометка уведомлений прочитанными**: Система предоставляет возможность явного указания уведомлений для пометки через
  их уникальные идентификаторы.

- **Подсчёт непрочитанных уведомлений**: API позволяет получить количество непрочитанных уведомлений, что необходимо для
  отображения значков или уведомлений о новых сообщениях на интерфейсе пользователя.

- **Отправка токенов устройств**: Для обеспечения возможности получения уведомлений на мобильные устройства
  предусмотрена отправка уникальных токенов устройств, что позволяет поддерживать актуальные данные для
  push-уведомлений. Токен передается вместе с ID устройства.

### Endpoints

#### /api/notifications/mark-as-read/

```http
mark_as_read = {
    "url": "/api/notifications/mark-as-read/",
    "method": "POST",
    "description": "Mark notifications as read by ids"
    "params": {
        "ids": 
            "type": "array",
            "description": "The ids of the notifications to mark as read"
    }
}

example:
{
    "ids": [1, 2, 3]
}
```

#### /api/notifications/get-notifications

```http
get_notifications = {
    "url": "/api/notifications/get-notifications",
    "method": "GET"
    "description": "Get all notifications for the current user"
    "params": {
        "page": "The requested page number",
        "per_page": "The number of notifications to return per page"
    }
}

example:
{
    "page": 1,
    "per_page": 10
}

response:
{
    "notifications": [
        {
            "id": 1,
            "title": "Notification title",
            "body": "Notification message",
            "sent_at": "2020-01-01T00:00:00Z",
            "read": false
        }
    ],
    "per_page": 10,
    "last_page": 5,
    "current_page": 1,
    "total": 50
}
```

#### /api/notifications/get-unread-notification-count

```http
get_unread_notification_count = {
    "url": "/api/notifications/get-unread-notification-count",
    "method": "GET"
    "description": "Get the number of unread notifications for the current user"
    "params": {}
}

response:
{
    "count": 1
}
```

#### /api/idm/send-device-token

```http
send_device_token = {
    "url": "/api/idm/send-device-token",
    "method": "POST"
    "description": "Send the device token to the server"
    "params": {
        "device_id": "The device unique ID",
        "device_token": "The device token to send"
    }
}
```
