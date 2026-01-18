<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ProductCollectionTest extends TestCase
{
    /** @test */
    public function it_can_add_products_to_collection()
    {
        $product1 = new Product(['name' => 'Product 1']);
        $product2 = new Product(['name' => 'Product 2']);

        $collection = new ProductCollection([$product1, $product2]);

        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($product1));
        $this->assertTrue($collection->contains($product2));
    }

    /** @test */
    public function it_can_be_filtered()
    {
        $product1 = new Product(['name' => 'Product 1']);
        $product2 = new Product(['name' => 'Product 2']);
        $collection = new ProductCollection([$product1, $product2]);

        $filtered = $collection->filter(function ($product) {
            return $product->name === 'Product 1';
        });

        $this->assertCount(1, $filtered);
        $this->assertTrue($filtered->contains($product1));
        $this->assertFalse($filtered->contains($product2));
    }

    /** @test */
    public function it_can_be_transformed()
    {
        $product1 = new Product(['name' => 'Product 1']);
        $product2 = new Product(['name' => 'Product 2']);
        $collection = new ProductCollection([$product1, $product2]);

        $transformed = $collection->map(function ($product) {
            return strtoupper($product->name);
        });

        $this->assertEquals(['PRODUCT 1', 'PRODUCT 2'], $transformed->toArray());
    }
}
