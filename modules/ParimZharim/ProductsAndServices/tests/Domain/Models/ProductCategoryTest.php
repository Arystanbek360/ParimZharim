<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_products()
    {
        $category = ProductCategory::factory()->create();
        $product1 = Product::factory()->create(['product_category_id' => $category->id]);
        $product2 = Product::factory()->create(['product_category_id' => $category->id]);

        $this->assertInstanceOf(Product::class, $category->products->first());
        $this->assertCount(2, $category->products);
    }

    /** @test */
    public function it_can_create_product_category_with_factory()
    {
        $category = ProductCategory::factory()->create([
            'name' => 'Test Category',
            'is_visible_to_customers' => true,
        ]);

        $this->assertDatabaseHas('products_and_services_product_categories', [
            'name' => 'Test Category',
            'is_visible_to_customers' => true,
        ]);
    }

    /** @test */
    public function it_soft_deletes_product_category()
    {
        $category = ProductCategory::factory()->create();

        $category->delete();

        $this->assertSoftDeleted($category);
    }
}
