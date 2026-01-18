<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\DocumentCollection;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;
use Modules\Shared\Documents\Domain\Services\DocumentService;
use Modules\Shared\Documents\Domain\Services\QueryService;
use Modules\Shared\Documents\Tests\TestCase;

class QueryServiceTest extends TestCase
{


    public function testGetDocumentsByQuery(): void
    {
        $document1 = $this->makeAndGetTestDocument(['type' => 'type1']);
        $document2 = $this->makeAndGetTestDocument(['type' => 'type2']);

        $queryParams = new DocumentQueryParams(types: ['type1']);
        $result = QueryService::getDocumentsByQuery($queryParams);

        $this->assertCount(1, $result);
        $this->assertEquals($document1->id, $result->first()->id);
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

        $history = QueryService::getDocumentHistory('DOC-001');

        // Проверяем, что возвращается правильная коллекция документов
        $this->assertInstanceOf(DocumentCollection::class, $history);
        $this->assertCount(3, $history);

        // Проверяем, что первая версия имеет номер 3, а последняя 1
        $this->assertEquals(3, $history->first()->version_number);
        $this->assertEquals(1, $history->last()->version_number);
    }

    public function testGetDocument(): void
    {
        $document = $this->makeAndGetTestDocument(['number' => 'DOC-001', 'type' => 'type1']);

        $result = QueryService::getDocument('DOC-001', 'type1');

        $this->assertEquals($document->id, $result->id);
    }

    public function testGetDocumentsByNumbers(): void
    {
        $document1 = $this->makeAndGetTestDocument(['number' => 'DOC-001', 'type' => 'type1']);
        $document2 = $this->makeAndGetTestDocument(['number' => 'DOC-002', 'type' => 'type2']);

        $result = QueryService::getDocumentsByNumbers(['DOC-001', 'DOC-002'], ['type1', 'type2']);

        $this->assertCount(2, $result);
        $this->assertEquals($document1->id, $result->first()->id);
        $this->assertEquals($document2->id, $result->last()->id);
    }

    public function testGetDocumentsByTypeAndDate(): void
    {
        $document1 = $this->makeAndGetTestDocument([
            'type' => 'type1',
            'date_from' => Carbon::now()->subDays(10),
            'date_to' => Carbon::now()->addDays(10)
        ]);
        $document2 = $this->makeAndGetTestDocument([
            'type' => 'type1',
            'date_from' => Carbon::now()->subDays(5),
            'date_to' => Carbon::now()->addDays(5)
        ]);

        $result = QueryService::getDocumentsByTypeAndDate('type1', Carbon::now()->subDays(15), Carbon::now()->addDays(15));

        $this->assertCount(2, $result);
    }

    public function testGetPackageById(): void
    {
        $package = Package::factory()->create();

        $result = QueryService::getPackageById($package->id);

        $this->assertEquals($package->id, $result->id);
    }

    public function testGetPackagesByQuery(): void
    {
        $package1 = Package::factory()->create(['type' => 'type1']);
        $package2 = Package::factory()->create(['type' => 'type2']);

        $queryParams = new PackageQueryParams(types: ['type1']);
        $result = QueryService::getPackagesByQuery($queryParams);

        $this->assertCount(1, $result);
        $this->assertEquals($package1->id, $result->first()->id);
    }
}
