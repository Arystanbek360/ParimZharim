<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Database\Factories\PackageFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\PackageCollection;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentPackageRepository;
use Modules\Shared\Documents\Tests\TestCase;

class EloquentPackageRepositoryTest extends TestCase
{


    private EloquentPackageRepository $repository;
    private array $testAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentPackageRepository();
        $this->testAttributes = PackageFactory::new()->make()->getAttributes();

        //$this->markTestIncomplete();
    }

    public function testSavePackage(): void
    {
        $package = PackageFactory::new()->make($this->testAttributes);
        $this->repository->savePackage($package);

        $this->assertDatabaseHas('documents_packages', [
            'id' => $package->id,
            'name' => $package->name,
            // Проверяем другие важные поля...
        ]);
    }

    public function testSaveDocumentInPackage(): void
    {
        $package = PackageFactory::new()->create($this->testAttributes);
        $document = $this->makeAndGetTestDocument();

        $this->repository->saveDocumentInPackage($document, $package);

        $this->assertEquals($package->id, $document->package_id);
        $this->assertDatabaseHas('documents_documents', [
            'id' => $document->id,
            'package_id' => $package->id,
        ]);
    }

    /**
     * @throws ValidationError
     */
    public function testGetPackageByQuery(): void
    {
        PackageFactory::new()->create($this->testAttributes);

        $queryParams = new PackageQueryParams(name: $this->testAttributes['name']);
        $packages = $this->repository->getPackageByQuery($queryParams);

        $this->assertInstanceOf(PackageCollection::class, $packages);
        $this->assertCount(1, $packages);
        $this->assertEquals($this->testAttributes['name'], $packages->first()->name);
    }
}
