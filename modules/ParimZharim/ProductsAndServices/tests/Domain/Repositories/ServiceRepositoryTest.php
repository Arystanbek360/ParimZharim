<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentServiceRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentServiceRepository $serviceRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceRepository = new EloquentServiceRepository();
    }

    public function testGetServicesByCategoryReturnsServiceCollection()
    {
        $categoryID = 1;

        // Создаем тестовые данные
        Service::factory()->count(5)->create(['service_category_id' => $categoryID, 'is_active' => true]);
        Service::factory()->count(2)->create(['service_category_id' => $categoryID, 'is_active' => false]); // неактивные сервисы

        $result = $this->serviceRepository->getServicesByCategory($categoryID);

        // Проверяем, что возвращенный объект является экземпляром ServiceCollection
        $this->assertInstanceOf(ServiceCollection::class, $result);
        // Проверяем, что возвращается 5 активных сервисов
        $this->assertCount(5, $result);
    }

    public function testGetAllServicesReturnsServiceCollection()
    {
        // Создаем тестовые данные
        Service::factory()->count(3)->create(['is_active' => true]);
        Service::factory()->count(2)->create(['is_active' => false]); // неактивные сервисы

        $result = $this->serviceRepository->getAllServices();

        // Проверяем, что возвращенный объект является экземпляром ServiceCollection
        $this->assertInstanceOf(ServiceCollection::class, $result);
        // Проверяем, что возвращается 3 активных сервиса
        $this->assertCount(3, $result);
    }
}
