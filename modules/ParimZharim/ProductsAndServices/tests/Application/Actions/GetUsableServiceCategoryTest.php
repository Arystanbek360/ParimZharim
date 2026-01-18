<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class GetUsableServiceCategoryTest extends TestCase
{
    private $serviceCategoryRepository;
    private $action;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок репозитория
        $this->serviceCategoryRepository = $this->createMock(ServiceCategoryRepository::class);
        // Создаем объект действия
        $this->action = new GetUsableServiceCategory($this->serviceCategoryRepository);
    }

    public function testHandleReturnsServiceCategoryCollection()
    {
        // Подготовка ожидаемой коллекции категорий услуг
        $expectedCollection = new ServiceCategoryCollection();
        // Настраиваем мок репозитория так, чтобы метод getUsableServiceCategories возвращал ожидаемую коллекцию
        $this->serviceCategoryRepository->method('getUsableServiceCategories')->willReturn($expectedCollection);

        // Вызов метода handle и проверка результата
        $result = $this->action->handle();
        $this->assertInstanceOf(ServiceCategoryCollection::class, $result, "The returned object should be an instance of ServiceCategoryCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }
}
