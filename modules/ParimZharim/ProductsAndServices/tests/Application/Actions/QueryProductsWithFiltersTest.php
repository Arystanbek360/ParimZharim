<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Application\Actions;

use Modules\ParimZharim\ProductsAndServices\Application\Actions\QueryProductsWithFilters;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class QueryProductsWithFiltersTest extends TestCase
{
    private $productRepository;
    private $action;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок репозитория
        $this->productRepository = $this->createMock(ProductRepository::class);
        // Создаем объект действия
        $this->action = new QueryProductsWithFilters($this->productRepository);
    }

    public function testHandleReturnsProductsByCategoryWhenCategoryIdIsProvided()
    {
        $categoryID = 1;
        $expectedCollection = new ProductCollection();
        // Настраиваем мок репозитория так, чтобы метод getProductsByCategory возвращал ожидаемую коллекцию
        $this->productRepository->method('getProductsByCategory')->with($categoryID)->willReturn($expectedCollection);

        // Вызов метода handle с categoryID и проверка результата
        $result = $this->action->handle($categoryID);
        $this->assertInstanceOf(ProductCollection::class, $result, "The returned object should be an instance of ProductCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }

    public function testHandleReturnsAllProductsWhenCategoryIdIsNotProvided()
    {
        $expectedCollection = new ProductCollection();
        // Настраиваем мок репозитория так, чтобы метод getAllProducts возвращал ожидаемую коллекцию
        $this->productRepository->method('getAllProducts')->willReturn($expectedCollection);

        // Вызов метода handle без categoryID и проверка результата
        $result = $this->action->handle();
        $this->assertInstanceOf(ProductCollection::class, $result, "The returned object should be an instance of ProductCollection.");
        $this->assertSame($expectedCollection, $result, "The returned collection should match the expected collection.");
    }
}
