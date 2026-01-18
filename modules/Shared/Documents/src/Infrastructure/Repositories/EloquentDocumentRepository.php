<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\DocumentCollection;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;
use Modules\Shared\Documents\Domain\Repositories\DocumentRepository;
use Modules\Shared\Documents\Infrastructure\Errors\CantRecreateModelError;

class EloquentDocumentRepository implements DocumentRepository
{
    protected const string TABLE = 'documents_documents';

    public function saveDocument(Document $document): void
    {
        $document->save();
    }

    public function saveDocumentQuietly(Document $document): void
    {
        $document->saveQuietly();
    }

    public function getMaxVersionNumber(Document $document): ?int
    {
        return DB::table(self::TABLE)
            ->where('number', $document->number)
            ->max('version_number');
    }

    public function getDocumentHistory(string $number, int $limit = 100, int $offset = 0): DocumentCollection
    {
        $documents = DB::table(self::TABLE)
            ->where('number', $number)
            ->orderBy('version_number', 'desc')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return new DocumentCollection($documents);
    }

    /**
     * @throws CantRecreateModelError
     */
    public function getDocumentsByQuery(DocumentQueryParams $queryParams): DocumentCollection
    {
        $results = $this->makeQuery($queryParams);
        return $this->makeDocumentCollection($results);
    }

    /**
     * Формирует и выполняет запрос.
     * @param DocumentQueryParams $queryParams Параметры для построения запроса.
     * @return array Возвращает массив из результатов запроса (данные найденных документов).
     */
    private function makeQuery(DocumentQueryParams $queryParams): array
    {
        $query = DB::table(self::TABLE);

        // Запросы whereIn
        if ($queryParams->ids !== null) {
            $query->whereIn('id', $queryParams->ids);
        }
        if ($queryParams->types !== null) {
            $query->whereIn('type', $queryParams->types);
        }
        if ($queryParams->statuses !== null) {
            $query->whereIn('status', $queryParams->statuses);
        }
        if ($queryParams->numbers !== null) {
            $query->whereIn('number', $queryParams->numbers);
        }
        if ($queryParams->creator_ids !== null) {
            $query->whereIn('creator_id', $queryParams->creator_ids);
        }
        if ($queryParams->package_ids !== null) {
            $query->whereIn('package_id', $queryParams->package_ids);
        }
        // Запрос where для 'name'
        if ($queryParams->name !== null) {
            $query->where('name', 'LIKE', "%{$queryParams->name}%");
        }
        // Запрос whereJsonContains для 'content'
        if ($queryParams->search !== null) {
            $query->whereJsonContains('content', $queryParams->search);
        }
        // Обработка тегов (если нужно реализовать вручную)
        if ($queryParams->tag_ids !== null) {
            $query->join('documents_tag_to_document', 'documents_documents.id', '=', 'documents_tag_to_document.document_id')
                  ->whereIn('documents_tag_to_document.tag_id', $queryParams->tag_ids);
            $query->groupBy('documents_documents.id');
        }

        // Запрос для 'date_from'
        if ($queryParams->date_from_from || $queryParams->date_from_to) {
            $start = $queryParams->date_from_from;
            $end = $queryParams->date_from_to;

            if ($start && $end) {
                $query->whereBetween('date_from', [$start, $end]);
            } elseif ($start) {
                $query->where('date_from', '>=', $start);
            } elseif ($end) {
                $query->where('date_from', '<=', $end);
            }
        }
        // Запрос для 'date_to'
        if ($queryParams->date_to_from || $queryParams->date_to_to) {
            $start = $queryParams->date_to_from;
            $end = $queryParams->date_to_to;

            if ($start && $end) {
                $query->whereBetween('date_to', [$start, $end]);
            } elseif ($start) {
                $query->where('date_to', '>=', $start);
            } elseif ($end) {
                $query->where('date_to', '<=', $end);
            }
        }

        // Выборка только последних версий, если в DocumentQueryParams указан флаг only_last_version
        if ($queryParams->only_last_version) {
            $query->join(DB::raw("(SELECT MAX(version_number) AS max_version, id
                               FROM " . self::TABLE . "
                               GROUP BY id) AS subquery"),
                function($join) {
                    $join->on('documents_documents.id', '=', 'subquery.id')
                        ->on('documents_documents.version_number', '=', 'subquery.max_version');
                });
        }
        $results = $query->select('documents_documents.*')->get();
        return $results->toArray();
    }

    /**
     * Создает коллекцию документов из результатов запроса.
     * @param array $dbResults Массив полученных из запроса результатов.
     * @return DocumentCollection Коллекция полученных документов.
     * @throws CantRecreateModelError Выбрасывает ошибку, если не удалось создать модель документа.
     */
    private function makeDocumentCollection(array $dbResults): DocumentCollection
    {
        $collection = new DocumentCollection();

        foreach ($dbResults as $result) {
            $attributes = (array)$result;
            $attributes['content'] = json_decode($attributes['content'], true);
            if (isset($attributes['metadata'])) {
                $attributes['metadata'] = json_decode($attributes['metadata'], true);
            } else {
                $attributes['metadata'] = [];
            }

            if (json_last_error() !== JSON_ERROR_NONE) {
                $error = "Ошибка получения Документов: невозможно декодировать содержимое и метаданные";
                throw new CantRecreateModelError($error);
            }
            $doc = Document::makeDocumentInstance($attributes);
            $collection[] = $doc;
        }
        return $collection;
    }
}
