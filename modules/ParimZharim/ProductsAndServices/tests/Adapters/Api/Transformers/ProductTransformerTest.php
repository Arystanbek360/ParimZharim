<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Adapters\Api\Transformers;

use Illuminate\Support\Facades\Storage;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\ProductTransformer;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ProductTransformerTest extends TestCase
{
    public function test_transform_product()
    {
        Storage::fake('local');

        $product = (new Product())->forceFill([
            'id' => 1,
            'name' => 'Test Product',
            'description' => 'This is a test product',
            'price' => 100.00,
            'image' => 'images/product.jpg',
            'product_category_id' => 2
        ]);

        $transformer = new ProductTransformer();

        $result = $transformer->transform($product);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('photo', $result);
        $this->assertArrayHasKey('category_id', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Test Product', $result['name']);
        $this->assertEquals('This is a test product', $result['description']);
        $this->assertEquals(100.00, $result['price']);
        $this->assertEquals(url('storage/images/product.jpg'), $result['photo']);
        $this->assertEquals(2, $result['category_id']);
    }

    public function test_transform_product_without_image()
    {
        $product = (new Product())->forceFill([
            'id' => 2,
            'name' => 'Another Product',
            'description' => 'This is another test product',
            'price' => 200.00,
            'image' => null,
            'product_category_id' => 3
        ]);

        $transformer = new ProductTransformer();

        $result = $transformer->transform($product);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('description', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('photo', $result);
        $this->assertArrayHasKey('category_id', $result);

        $this->assertEquals(2, $result['id']);
        $this->assertEquals('Another Product', $result['name']);
        $this->assertEquals('This is another test product', $result['description']);
        $this->assertEquals(200.00, $result['price']);
        $this->assertNull($result['photo']);
        $this->assertEquals(3, $result['category_id']);
    }
}
