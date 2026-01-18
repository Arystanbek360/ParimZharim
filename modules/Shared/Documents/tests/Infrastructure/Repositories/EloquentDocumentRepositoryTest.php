<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\DocumentCollection;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Domain\Services\DocumentService;
use Modules\Shared\Documents\Infrastructure\Errors\CantRecreateModelError;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentDocumentRepository;
use Modules\Shared\Documents\Tests\TestCase;

class EloquentDocumentRepositoryTest extends TestCase
{


    protected EloquentDocumentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentDocumentRepository();
    }

    public function testSaveDocument(): void
    {
        $document = $this->makeAndGetTestDocument(andSave: false);
        $this->repository->saveDocument($document);

        $this->assertDatabaseHas('documents_documents', [
            'id' => $document->id,
            'number' => $document->number,
        ]);
    }

    /**
     * @throws ValidationError
     */
    public function testGetDocumentHistory(): void
    {
        // Создаем документ с разными версиями
        $document = $this->makeAndGetTestDocument(['number' => 'DOC-001', 'version_number' => 1]);
        $document = DocumentService::createNewVersion($document);
        $document = DocumentService::createNewVersion($document);

        $history = $this->repository->getDocumentHistory('DOC-001');

//        foreach ($history as $historyItem)
//        {
//            print_r(json_encode($historyItem, JSON_UNESCAPED_UNICODE) . "\n");
//        }

        // Проверяем, что возвращается правильная коллекция документов
        $this->assertInstanceOf(DocumentCollection::class, $history);
        $this->assertCount(3, $history);

        // Проверяем, что первая версия имеет номер 3, а последняя 1
        $this->assertEquals(3, $history->first()->version_number);
        $this->assertEquals(1, $history->last()->version_number);
    }

    /**
     * @throws CantRecreateModelError
     * @throws ValidationError
     */
    public function testGetDocumentsByQuery(): void
    {
        $document1 = $this->makeAndGetTestDocument(['type' => 'type1', 'status' => 'status1']);
        $document2 = $this->makeAndGetTestDocument(['type' => 'type2', 'status' => 'status2']);

        $queryParams = new DocumentQueryParams(
            types: ['type1']
        );

        $result = $this->repository->getDocumentsByQuery($queryParams);

        // Выводим результат для отладки
        print_r(json_encode($result->first(), JSON_UNESCAPED_UNICODE) . "\n"); //die;

        $this->assertInstanceOf(DocumentCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($document1->id, $result->first()->id);
    }

    /**
     * @throws CantRecreateModelError
     * @throws ValidationError
     */
    public function testGetDocumentsByQueryWithTags(): void
    {
        $document = $this->makeAndGetTestDocument();
        $tag = Tag::factory()->create();
        $document->tags()->attach($tag->id);

        $queryParams = new DocumentQueryParams(
            tag_ids: [$tag->id]
        );

        $result = $this->repository->getDocumentsByQuery($queryParams);

        $this->assertInstanceOf(DocumentCollection::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals($document->id, $result->first()->id);
    }

    /**
     * @throws CantRecreateModelError
     * @throws ValidationError
     */
    public function testGetDocumentsByQueryWithDateRange()
    {
        // Создаем документы с разными значениями даты
        $document = $this->makeAndGetTestDocument();
        $document->save();
        $document1 = $this->makeAndGetTestDocument(['date_from' => now()->subDays(10), 'date_to' => now()->subDays(5)]);
        $document2 = $this->makeAndGetTestDocument(['date_from' => now()->subDays(3), 'date_to' => now()->addDays(1)]);

        // Создаем экземпляр DocumentQueryParams с установленными диапазонами дат
        $queryParams = new DocumentQueryParams(
            date_from_from: now()->subDays(7),
            date_from_to: now()
        );

        // Выполняем запрос через репозиторий
        $result = $this->repository->getDocumentsByQuery($queryParams);

        // Проверяем, что результат содержит только второй документ
        $this->assertCount(1, $result);

        $retrievedDocument = $result->first();
        $this->assertSame($document2->id, $retrievedDocument->id);
        $this->assertSame($document2->name, $retrievedDocument->name);
        $this->assertSame($document2->number, $retrievedDocument->number);
        $this->assertSame($document2->type, $retrievedDocument->type);
        $this->assertSame($document2->status, $retrievedDocument->status);
        $this->assertSame($document2->date_from->toDateTimeString(), $retrievedDocument->date_from->toDateTimeString());
        $this->assertSame($document2->date_to->toDateTimeString(), $retrievedDocument->date_to->toDateTimeString());
        // Добавьте проверки других важных атрибутов
    }
}
