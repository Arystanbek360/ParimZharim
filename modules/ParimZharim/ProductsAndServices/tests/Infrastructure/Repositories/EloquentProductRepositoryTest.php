<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentProductRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class EloquentProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProductRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProductRepository();
    }

    /** @test */
    public function it_returns_products_by_category()
    {
        // Arrange
        $categoryID = 1;
        $activeProduct1 = Product::factory()->create(['product_category_id' => $categoryID, 'is_active' => true]);
        $activeProduct2 = Product::factory()->create(['product_category_id' => $categoryID, 'is_active' => true]);
        $inactiveProduct = Product::factory()->create(['product_category_id' => $categoryID, 'is_active' => false]);
        $otherCategoryProduct = Product::factory()->create(['product_category_id' => 2, 'is_active' => true]);

        // Act
        $result = $this->repository->getProductsByCategory($categoryID);

        // Assert
        $this->assertInstanceOf(ProductCollection::class, $result);
        $this->assertCount(2, $result);  // Ожидаем только два активных продукта
    }

    /** @test */
    public function it_returns_all_active_products()
    {
        // Arrange
        $activeProduct1 = Product::factory()->create(['is_active' => true]);
        $activeProduct2 = Product::factory()->create(['is_active' => true]);
        $inactiveProduct1 = Product::factory()->create(['is_active' => false]);
        $inactiveProduct2 = Product::factory()->create(['is_active' => false]);

        // Act
        $result = $this->repository->getAllProducts();

        // Assert
        $this->assertInstanceOf(ProductCollection::class, $result);
        $this->assertCount(2, $result);  // Ожидаем только два активных продукта
    }
}
