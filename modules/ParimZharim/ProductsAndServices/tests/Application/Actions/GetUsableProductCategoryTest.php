<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Application\Actions\GetUsableProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class GetUsableProductCategoryTest extends TestCase
{
    private $productCategoryRepository;
    private $action;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок репозитория
        $this->productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
        // Создаем объект действия
        $this->action = new GetUsableProductCategory($this->productCategoryRepository);
    }

    public function testHandleReturnsProductCategoryCollection()
    {
        // Подготовка ожидаемой коллекции категорий продуктов
        $expectedCollection = new ProductCategoryCollection();
        // Настраиваем мок репозитория так, чтобы метод getUsableProductCategories возвращал ожидаемую коллекцию
        $this->productCategoryRepository->method('getUsableProductCategories')->willReturn($expectedCollection);

        // Вызов метода handle и проверка результата
        $result = $this->action->handle();
        $this->assertInstanceOf(ProductCategoryCollection::class, $result, "The returned object should be an instance of ProductCategoryCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }
}
