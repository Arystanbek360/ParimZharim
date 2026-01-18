# Документация по Data Model модуля "Документы" (Documents)

## Введение

Эта документация описывает структуру данных для модуля "Документы". Модуль обеспечивает создание, хранение, управление и
отслеживание жизненного цикла различных типов документов в системе. Он поддерживает версионирование, тегирование,
управление доступом и интеграцию с другими модулями.

## Схема:

```mermaid
classDiagram
    class  Document {
        <<abstract>>
        +int id
        +string name
        +string number
        +string type
        +string status
        +int version_number
        +int creator_id
        +?int package_id
        +?Package package
        +?string file
        +array content
        +array metadata
        +Carbon date_from
        +?Carbon date_to
        +BelongsToMany tags
        +BelongsToMany users
        #abstract getContentStructure()
        #validateContentStructure() : bool
    }

    class Package {
        +int id
        +string name
        +string type
        +string status
        +int creator_id
        +?int parent_package_id
        +array metadata
        +BelongsToMany users
    }

    class Tag {
        +int id
        +string name
        +BelongsToMany documents
    }

    class AccessTrait {
        +AccessMode access_mode
        +AccessType default_access_type
    }

    class DocumentToUser {
        +int document_id
        +int user_id
        +AccessType access_type
    }

    class PackageToUser {
        +int package_id
        +int user_id
        +AccessType access_type
    }

    class AccessType {
        <<enumeration>>
        +string value (READ, COMMENT, WRITE)
    }
    
    class AccessMode {
        <<enumeration>>
        +string value (AnyUser, SpecificUsers)
    }
    
    class DocumentQueryParams {
        <<ValueObject>>
        +?string name
        +?string search
        +?array numbers
        +?array types
        +?array statuses
        +?array ids
        +?array creator_ids
        +?array package_ids
        +?array tag_ids
        +?Carbon date_from_from
        +?Carbon date_from_to
        +?Carbon date_to_from
        +?Carbon date_to_to
        +bool only_last_version
    }
    
    class PackageQueryParams {
        <<ValueObject>>
        +?string name
        +?array ids
        +?array creator_ids
        +?int parent_package_id
    }
   
    Document --|> AccessTrait : наследует свойства
    Package --|> AccessTrait : наследует свойства

    Document "*" --> "*" Tag : помечаются
    Package "1" --o "*" Document : содержит
    Package "1" --o "*" Package : является родителем
    Document "1" --> "*" DocumentToUser
    Package "1" --> "*" PackageToUser

    AccessTrait --o AccessMode : включает
    AccessTrait --o AccessType : включает
    
    DocumentToUser --> AccessType : имеет
    PackageToUser --> AccessType : имеет

```

## Сущности

### Документ (Document)

Абстрактный класс. Основная сущность, представляющая документ в системе. Включает:

- `id`: Уникальный идентификатор документа.
- `name`: Название документа.
- `number`: Уникальный номер документа.
- `type`: Тип документа.
- `status`: Статус документа.
- `version_number`: Номер версии документа.
- `creator_id`: Идентификатор создателя документа.
- `package_id`: Идентификатор пакета, к которому относится документ (необязательное поле).
- `Package`: Связь с пакетом документов.
- `file`: Путь к файлу (необязательное поле).
- `content`: Произвольный контент в формате JSONB.
- `metadata`: Метаданные в формате JSONB.
- `date_from`: Дата начала действия документа.
- `date_to`: Дата окончания действия документа (необязательное поле).
- `tags`: Связь с тегами (многие ко многим).
- `users`: Связь с пользователями (многие ко многим).

### Пакет (Package)

Группа документов, объединенная по определенным признакам. Содержит:

- `id`: Уникальный идентификатор пакета.
- `name`: Название пакета.
- `type`: Тип пакета документов.
- `status`: Статус пакета.
- `creator_id`: Идентификатор создателя пакета.
- `parent_package_id`: Идентификатор родительского пакета (необязательное поле).
- `metadata`: Метаданные пакета в формате JSONB.

### Тег (Tag)

Используется для классификации документов. Содержит:

- `id`: Уникальный идентификатор тега.
- `name`: Название тега.

### Доступ (AccessTrait)

Описывает модель доступа к документам. Может быть:

- `access_mode`: Режим доступа (для конкретных пользователей или для всех).
- `default_access_type`: Тип доступа по умолчанию (чтение или запись).

### Связи между документами и пользователями (Document_to_User, Package_to_User)

Описывают права доступа пользователей к документам. Содержат:

- `document_id`: Идентификатор документа.
- `user_id`: Идентификатор пользователя.
- `access_type`: Тип доступа (чтение, комментирование или запись).

### Связи между пакетами и пользователями (Package_to_User)

Описывают права доступа пользователей к документам. Содержат:

- `package_id`: Идентификатор документа.
- `user_id`: Идентификатор пользователя.
- `access_type`: Тип доступа (чтение, комментирование или запись).

### Перечисления (AccessMode, AccessType)

Описывают возможные значения для режима доступа и типа доступа.

## Объекты-значения

### DocumentQueryParams

Этот объект-значение используется для инкапсуляции параметров запроса при поиске документов. Он обеспечивает строгую
валидацию входящих данных, чтобы гарантировать корректность запросов к базе данных.

#### Структура

- `name`: Название документа (опционально).
- `search`: Строка для поиска по содержимому документа (опционально).
- `numbers`: Массив номеров документов (опционально).
- `types`: Массив типов документов (опционально).
- `statuses`: Массив статусов документов (опционально).
- `ids`: Массив идентификаторов документов (опционально).
- `creator_ids`: Массив идентификаторов создателей (опционально).
- `package_ids`: Массив идентификаторов пакетов (опционально).
- `tag_ids`: Массив идентификаторов тегов (опционально).
- `date_from_from`: Начальная граница даты действия документа (опционально).
- `date_from_to`: Конечная граница даты начала действия документа (опционально).
- `date_to_from`: Начальная граница даты окончания действия документа (опционально).
- `date_to_to`: Конечная граница даты окончания действия документа (опционально).
- `only_last_version`: Флаг для настройки запроса для получения только последних версий документов (опционально).

#### Валидация

Для обеспечения корректности и целостности данных, `DocumentQueryParams` включает следующие проверки:

- **Дата**: Проверяется, что значения дат `date_from_from`, `date_from_to`, `date_to_from`, и `date_to_to` соответствуют
  корректному формату и логически правильно распределены (например, начальная дата не позже конечной).
- **Идентификаторы**: Все идентификаторы в массивах `ids`, `creator_ids`, `package_ids`, и `tag_ids` должны быть
  натуральными числами.
- **Строки**: Строковые параметры, такие как `name` и `search`, проверяются на соответствие допустимым форматам и длине.

Эти проверки помогают избежать ошибок при формировании запросов к базе данных и улучшают безопасность и надежность
работы системы.

### PackageQueryParams

Этот объект-значение используется для инкапсуляции параметров запроса при поиске пакетов документов. Он позволяет точно
и безопасно фильтровать пакеты по заданным критериям.

#### Структура

- `ids`: Массив идентификаторов пакетов (опционально).
- `name`: Название пакета (опционально).
- `types`: Тип пакета (опционально).
- `statuses`: Статус пакета (опционально).
- `creator_ids`: Массив идентификаторов создателей пакетов (опционально).
- `parent_package_id`: Идентификатор родительского пакета (опционально).

#### Валидация

Для поддержания целостности данных и предотвращения ошибок в запросах, `PackageQueryParams` включает следующие проверки:

- **Идентификаторы**: Проверяется, что все идентификаторы в массивах `ids`, `creator_ids`, а также `parent_package_id`
  являются натуральными числами.
- **Строки**: Параметр `name` проверяется на соответствие допустимому формату строки и длине, чтобы избежать ввода
  некорректных данных.
