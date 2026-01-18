# Laravel Nova Load Testing

Этот скрипт предназначен для проведения нагрузочного тестирования админок Laravel Nova с использованием Locust и Selenium. Проект позволяет аутентифицироваться на сайте, посещать предопределенные URL и логировать результаты запросов.

## Содержание

- [Структура проекта](#структура-проекта)
- [Требования](#требования)
- [Установка](#установка)
- [Использование](#использование)
- [Конфигурация](#конфигурация)

## Структура проекта

```
├── config.yaml
├── nova.py
├── nova.sh
└── README.md
```

## Требования

- Python 3.x
- ChromeDriver (совместимый с установленной версией Google Chrome и операционной системы) - должен лежать по пути ./chromedriver
- Виртуальное окружение Python (venv)

## Установка

1. Клонируйте репозиторий или скачайте файлы проекта.
2. Убедитесь, что у вас установлен Python 3 и ChromeDriver.
3. Установите зависимости, запустив скрипт `nova.sh`, который автоматически создаст виртуальное окружение и установит все необходимые библиотеки.

## Использование

1. Настройте файл конфигурации `config.yaml` (скопируйте из config.example.yaml):

```yaml
login_url: 'http://stage.parimzharim.devcraft.meteoro.ai/nova/login/'
username: 'super@admin.com'
password: '1jqjqa'
urls:
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/orderable-product-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/product-category-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/orderable-service-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/service-category-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/plan-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/resources/order-admin-resources'
  - 'https://stage.parimzharim.devcraft.meteoro.ai/nova/reservations-tool'
```

2. Запустите скрипт `nova.sh`:

```bash
./nova.sh
```

Скрипт выполнит следующие действия:
- Создаст виртуальное окружение, если оно еще не создано.
- Активирует виртуальное окружение.
- Установит все необходимые зависимости.
- Запустит Locust для проведения нагрузочного тестирования.

## Конфигурация

### config.yaml

Этот файл содержит параметры для входа на сайт и список URL, которые необходимо посетить. Пример структуры:

```yaml
login_url: 'URL для входа в систему'
username: 'Имя пользователя для входа'
password: 'Пароль для входа'
urls:
  - 'URL страницы 1'
  - 'URL страницы 2'
  - ...
```

### nova.py

Этот скрипт содержит реализацию тестов, используя Locust и Selenium для выполнения аутентификации и посещения страниц.

### nova.sh

Скрипт для автоматической установки зависимостей и запуска тестов.

## Логирование

Скрипт `nova.py` логирует результаты запросов, включая успешные и неуспешные запросы, с помощью встроенных возможностей Locust. Все логи записываются в стандартный вывод, что позволяет отслеживать процесс тестирования.
