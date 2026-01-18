## Тестирование получения тегов объектов по API

### Описание

**Цель**: Проверка функциональности получения тегов объектов по API.

Тестирование получения списка тегов объектов с сервера через API и проверка формата ответа.

### Предусловия и необходимые инструменты

- Доступ к API с правами на выполнение GET запросов.
- Инструмент для выполнения API запросов, например Postman или cURL.

### Тестовые данные
- URL запроса: `{{host}}/api/objects/get-object-tags`

### Шаги

#### Шаг 1:
Выполнить GET запрос к API по URL `{{host}}/api/objects/get-object-tags`.
**Ожидаемый результат**: Сервер возвращает ответ со статусом 200 OK и список тегов в формате JSON.

### Негативные сценарии

#### Сценарий 1:
Выполнить GET запрос к несуществующему URL `{{host}}/api/objects/get-nonexistent-endpoint`.
**Ожидаемый результат**: Сервер возвращает ответ со статусом 404 Not Found.


### Постусловия

- Нет необходимости в постусловиях, так как запрос является только для чтения данных.

### Замечания/Примечания

- Убедиться, что формат JSON ответа соответствует ожидаемому.
- Проверить, что все обязательные поля (id, name, img) присутствуют в каждом объекте списка.

### Пример ожидаемого ответа для запроса тегов объектов
```json
[
    {
        "id": 1,
        "name": "Камин",
        "img": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/tags/apJlUO36G5mxOwPJS3byFLkNHPjHTwCbk4YJ1t0m.svg"
    },
    {
        "id": 3,
        "name": "Караоке",
        "img": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/tags/f3SRpKas0zqG8lBwtZg9gmjXyzbyuOG6gDCDvyuy.svg"
    },
    {
        "id": 5,
        "name": "Ledi",
        "img": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/tags/dq62NXbCPrECZsmmeoB55DvZfxZfHmVT4XEo8n7h.svg"
    },
    {
        "id": 4,
        "name": "Сауна",
        "img": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/tags/saTzeYDpjpxUQBoUaYBzPffgfP8qUGC3oZV1uyMq.svg"
    },
    {
        "id": 2,
        "name": "Бар",
        "img": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/tags/PtZycjN5cFifmoZkALL74Wi4twnKscuBsY0FNccr.svg"
    }
]
```
