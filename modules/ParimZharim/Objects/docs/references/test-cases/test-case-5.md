## Тестирование получения категорий объектов по API

### Описание

**Цель**: Проверка функциональности получения категорий объектов по API.

Тестирование получения списка категорий объектов с сервера через API и проверка формата ответа.

### Предусловия и необходимые инструменты

- Доступ к API с правами на выполнение GET запросов.
- Инструмент для выполнения API запросов, например Postman или cURL.

### Тестовые данные
- URL запроса: `{{host}}/api/objects/get-object-categories`

### Шаги

#### Шаг 1:
Выполнить GET запрос к API по URL `{{host}}/api/objects/get-object-categories`.
**Ожидаемый результат**: Сервер возвращает ответ со статусом 200 OK и список категорий в формате JSON.

### Негативные сценарии

#### Сценарий 1:
Выполнить GET запрос к несуществующему URL `{{host}}/api/objects/get-nonexistent-endpoint`.
**Ожидаемый результат**: Сервер возвращает ответ со статусом 404 Not Found.


### Постусловия

- Нет необходимости в постусловиях, так как запрос является только для чтения данных.

### Замечания/Примечания

- Убедиться, что формат JSON ответа соответствует ожидаемому.
- Проверить, что все обязательные поля (type, name, photo) присутствуют в каждом объекте списка.

### Пример ожидаемого ответа для запроса категорий объектов

```json
[
    {
        "type": 1,
        "name": "Гриль-Домики",
        "photo": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/categories/GWX41tdP9Gxl34Car96W8JVWvHAvCrYyPlIC4Cup.jpg"
    },
    {
        "type": 2,
        "name": "Бани",
        "photo": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/categories/Lwt1GtFKa4rffloyXAVusViKDgwfw2CJCwd8ZYKu.png"
    },
    {
        "type": 3,
        "name": "Беседки",
        "photo": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/categories/97jqIX3gKjzcOfcMhJZbxrEovCDnQo6ylm9dUChs.png"
    }
]
```
