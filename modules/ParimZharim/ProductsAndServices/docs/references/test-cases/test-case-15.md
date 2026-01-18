
## Тестирование API для получения списка меню с объединением по категориям

### Описание

**Цель**: Проверить возможность получения списка меню с объединением по категориям через API.

Проверка корректности работы API по запросу списка меню по заданной категории и проверки структуры ответа.

### Предусловия и необходимые инструменты

- Доступ к API с установленными правами для запроса списка меню по категории
- Инструмент для тестирования API, например, Postman

### Тестовые данные
- URL для запроса: {{host}}/api/products-services/get-products-by-category?category_id=6

### Шаги

#### Шаг 1:
Отправить GET запрос на URL: {{host}}/api/products-services/get-products-by-category?category_id=6.
**Ожидаемый результат**: Статус ответа 200 ОК и тело ответа в формате JSON.

#### Шаг 2:
Проверить структуру ответа.
**Ожидаемый результат**: Ответ должен содержать JSON массив со следующими объектами:
```json
[
    {
        "id": 5,
        "name": "Кусочки гоблина",
        "description": "вкуснааа",
        "price": "1.00",
        "photo": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/products/CIGgXDruwhtvHG6bjyUxACKEyfNZxU7PQ0JYLLp3.png",
        "category_id": 6
    },
    {
        "id": 4,
        "name": "борщик",
        "description": "вкусный борщик",
        "price": "9999.00",
        "photo": "https://meteoro-development-space.ams3.digitaloceanspaces.com/parimzharim-develop/products/CeVGPP5ebqBjcW1YWfwbmsAktCRmtAOlc8bHDmlO.png",
        "category_id": 6
    }
]
```

### Постусловия

- Нет, так как запрос не изменяет состояние системы.

### Замечания/Примечания

- Убедитесь, что у вас есть доступ к API и корректные права для выполнения данного запроса.
- Структура ответа может изменяться в зависимости от обновлений API, поэтому проверка должна учитывать возможные изменения.
