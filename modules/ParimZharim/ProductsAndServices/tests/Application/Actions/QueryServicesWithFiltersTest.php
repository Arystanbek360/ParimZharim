<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryServicesWithFilters;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class QueryServicesWithFiltersTest extends TestCase
{
    private $serviceRepository;
    private $action;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок репозитория
        $this->serviceRepository = $this->createMock(ServiceRepository::class);
        // Создаем объект действия
        $this->action = new QueryServicesWithFilters($this->serviceRepository);
    }

    public function testHandleReturnsServicesByCategoryWhenCategoryIdIsProvided()
    {
        $categoryID = 1;
        $expectedCollection = new ServiceCollection();
        // Настраиваем мок репозитория так, чтобы метод getServicesByCategory возвращал ожидаемую коллекцию
        $this->serviceRepository->method('getServicesByCategory')->with($categoryID)->willReturn($expectedCollection);

        // Вызов метода handle с categoryID и проверка результата
        $result = $this->action->handle($categoryID);
        $this->assertInstanceOf(ServiceCollection::class, $result, "The returned object should be an instance of ServiceCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }

    public function testHandleReturnsAllServicesWhenCategoryIdIsNotProvided()
    {
        $expectedCollection = new ServiceCollection();
        // Настраиваем мок репозитория так, чтобы метод getAllServices возвращал ожидаемую коллекцию
        $this->serviceRepository->method('getAllServices')->willReturn($expectedCollection);

        // Вызов метода handle без categoryID и проверка результата
        $result = $this->action->handle();
        $this->assertInstanceOf(ServiceCollection::class, $result, "The returned object should be an instance of ServiceCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }
}
