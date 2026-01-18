<?php
namespace Modules\ParimZharim\ProductsAndServices\Tests\Infrastructure\Repositories;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentServiceRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;

class EloquentServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentServiceRepository();
    }

    public function testGetServicesByCategory()
    {
        // Arrange
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create([
            'service_category_id' => $category->id,
            'is_active' => true,
        ]);

        // Act
        $services = $this->repository->getServicesByCategory($category->id);

        // Assert
        $this->assertInstanceOf(ServiceCollection::class, $services);
        $this->assertCount(1, $services);
        $this->assertEquals($service->id, $services->first()->id);
    }

    public function testGetAllServices()
    {
        // Arrange
        $activeService = Service::factory()->create(['is_active' => true]);
        $inactiveService = Service::factory()->create(['is_active' => false]);

        // Act
        $services = $this->repository->getAllServices();

        // Assert
        $this->assertInstanceOf(ServiceCollection::class, $services);
        $this->assertCount(1, $services);
        $this->assertEquals($activeService->id, $services->first()->id);
    }
}
