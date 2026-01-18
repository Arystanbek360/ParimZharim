# Репозитории для модуля "Документы"

## Введение

Репозитории модуля "Документы" обеспечивают доступ к базе данных, скрывая сложности SQL-запросов и операций с базой
данных. Они предоставляют методы для сохранения, обновления и запроса данных документов, обеспечивая таким образом
поддержку операций, определенных на уровне сервисов.

## Репозитории

### DocumentRepository

#### Методы:

- `getDocumentsByQuery(DocumentQueryParams $queryParams)`: Возвращает список документов на основе заданных параметров
  поиска. Этот метод позволяет выполнить поиск по различным атрибутам документа и их комбинациям.

  **Параметры запроса:**
  DocumentQueryParams - объект, содержащий параметры запроса.
    - `name`: Имя или часть имени документа.
    - `numbers`: Массив номеров документов.
    - `ids`: Массив идентификаторов документов.
    - `creator_ids`: Массив идентификаторов создателей документов.
    - `types`: Массив типов документов.
    - `package_ids`: Массив идентификаторов пакетов, к которым относятся документы.
    - `statuses`: Массив статусов документов.
    - `date_from_from`: Дата начала действия документа (от).
    - `date_from_to`: Дата начала действия документа (до).
    - `date_to_from`: Дата окончания действия документа (от).
    - `date_to_to`: Дата окончания действия документа (до).
    - `search`: Поиск по произвольному контенту в формате JSONB.
    - `tags`: Массив идентификаторов тегов.
    - `only_last_version`: Флаг настройки запроса для выборки только последних версий.

  **Пример использования:**
  ```php
    
    $queryParams = new DocumentQueryParams(
        [
            'name' => 'Document',
            'numbers' => [1, 2, 3],
            'ids' => [1, 2, 3],
            'creator_ids' => [1, 2],
            'types' => ['type1', 'type2'],
            'package_ids' => [1, 2],
            'statuses' => ['active', 'inactive'],
            'date_from_from' => '2021-01-01',
            'date_from_to' => '2021-12-31',
            'date_to_from' => '2021-01-01',
            'date_to_to' => '2021-12-31',
            'search' => 'search',
            'tags' => [1, 2, 3],
            'only_last_version' => false  
        ]);
   
    $documents = $documentRepository->getDocumentsByQuery($queryParams);
    ```

- `getDocumentHistory(int documentID, limit = 100, offset = 0)`: Возвращает историю версий документа по его
  идентификатору.

  **Параметры запроса:**
    - `DocumentID`: Идентификатор документа.

  **Пример использования:**
  ```php
    $documentID = 1;
    $documentHistory = $documentRepository->getDocumentHistory($documentID);
    ```

- `saveDocument(Document document)`: Сохраняет документ в базе данных.

  **Параметры запроса:**
    - `Document`: Объект документа для сохранения.

  **Пример использования:**
  ```php
    $document = new Document();
    $document->setName('Document 1');
    $document->setNumber('1');
    $document->setType('type1');
    $document->setStatus('active');
    $document->setCreatorID(1);
    $document->setPackageID(1);
    $document->setDateFrom('2021-01-01');
    $document->setDateTo('2021-12-31');
    $document->setContent('{"key": "value"}');
    
    $documentRepository->saveDocument($document);
    ```

### PackageRepository

#### Методы:

- `saveDocumentInPackage(Document, Package)`: Сохранение документа в конкретный пакет.
- `getPackageByQuery(PackageQueryParams $queryParams)`: Возвращает список пакетов на основе заданных параметров поиска.
  **Параметры запроса:**
  PackageQueryParams - объект, содержащий параметры запроса.
    - `name`: Имя или часть имени пакета.
    - `ids`: Массив идентификаторов пакетов.
    - `creator_ids`: Массив идентификаторов создателей пакетов.
    - `parent_package_id`: Идентификатор родительского пакета.
    - `types`: Массив типов пакетов.

  **Пример использования:**
  ```php
    $queryParams = new PackageQueryParams(
        [
            'name' => 'Package',
            'ids' => [1, 2, 3],
            'creator_ids' => [1, 2],
            'parent_package_id' => 1,
            'types' => ['type1', 'type2']
        ]);
  
    $packages = $packageRepository->getPackageByQuery($queryParams);
    ```

### TagRepository

#### Методы:

- `getTags(limit = 100, offset = 0)`: Возвращает список всех доступных тегов.
- `getTagsByIds(tagIds)`: Возвращает тег по его идентификатору.

## Обработка исключений

Репозитории также отвечают за обработку исключений, возникающих в результате запросов к базе данных, обеспечивая
надежность и стабильность работы модуля "Документы".
