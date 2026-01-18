<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_product_correctly()
    {
        // Создаем категорию продукта
        $productCategory = ProductCategory::factory()->create();

        // Создаем продукт, привязанный к этой категории
        $product = Product::factory()->create([
            'product_category_id' => $productCategory->id,
            'name' => 'Sample Product',
            'description' => 'A sample product description',
            'price' => 99.99,
            'is_active' => true
        ]);

        // Проверяем, что продукт был создан
        $this->assertDatabaseHas('products_and_services_products', [
            'id' => $product->id,
            'name' => 'Sample Product',
            'price' => 99.99
        ]);

        // Проверяем связь с категорией продукта
        $this->assertEquals($productCategory->id, $product->productCategory->id);

        // Проверяем значения полей
        $this->assertEquals('Sample Product', $product->name);
        $this->assertEquals(99.99, $product->price);
        $this->assertTrue($product->is_active);
        $this->assertEquals('A sample product description', $product->description);
    }
}
