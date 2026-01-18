<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentProductCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class EloquentProductCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentProductCategoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new EloquentProductCategoryRepository();
    }

    /** @test */
    public function it_returns_usable_product_categories()
    {
        // Создаем видимую категорию с продуктами
        ProductCategory::factory()
            ->has(\Modules\ParimZharim\ProductsAndServices\Domain\Models\Product::factory()->count(3), 'products')
            ->create(['is_visible_to_customers' => true]);

        // Создаем невидимую категорию с продуктами
        ProductCategory::factory()
            ->has(\Modules\ParimZharim\ProductsAndServices\Domain\Models\Product::factory()->count(3), 'products')
            ->create(['is_visible_to_customers' => false]);

        // Создаем видимую категорию без продуктов
        ProductCategory::factory()
            ->create(['is_visible_to_customers' => true]);

        $result = $this->repository->getUsableProductCategories();

        $this->assertInstanceOf(ProductCategoryCollection::class, $result);
        $this->assertCount(1, $result);  // Ожидаем только одну категорию, которая видима и имеет продукты
        foreach ($result as $category) {
            $this->assertTrue($category->is_visible_to_customers);
            $this->assertGreaterThan(0, $category->products->count());
        }
    }
}