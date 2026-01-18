<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;

class ProductCategoryRepositoryTest extends TestCase
{
    private $productCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок реализации ProductCategoryRepository
        $this->productCategoryRepository = $this->createMock(ProductCategoryRepository::class);
    }

    public function testGetUsableProductCategoriesReturnsProductCategoryCollection()
    {
        // Подготовка возвращаемого значения метода getUsableProductCategories
        $expectedCollection = new ProductCategoryCollection();
        $this->productCategoryRepository->method('getUsableProductCategories')->willReturn($expectedCollection);

        // Вызов метода getUsableProductCategories и проверка результата
        $result = $this->productCategoryRepository->getUsableProductCategories();
        $this->assertInstanceOf(ProductCategoryCollection::class, $result, "The returned object should be an instance of ProductCategoryCollection.");
    }
}
