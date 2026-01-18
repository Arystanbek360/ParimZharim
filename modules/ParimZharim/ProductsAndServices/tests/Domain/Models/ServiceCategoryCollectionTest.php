<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceCategoryCollectionTest extends TestCase
{
    /** @test */
    public function it_can_add_service_categories_to_collection()
    {
        $category1 = new ServiceCategory(['name' => 'Category 1']);
        $category2 = new ServiceCategory(['name' => 'Category 2']);

        $collection = new ServiceCategoryCollection([$category1, $category2]);

        $this->assertCount(2, $collection);
        $this->assertTrue($collection->contains($category1));
        $this->assertTrue($collection->contains($category2));
    }

    /** @test */
    public function it_can_be_filtered()
    {
        $category1 = new ServiceCategory(['name' => 'Category 1']);
        $category2 = new ServiceCategory(['name' => 'Category 2']);
        $collection = new ServiceCategoryCollection([$category1, $category2]);

        $filtered = $collection->filter(function ($category) {
            return $category->name === 'Category 1';
        });

        $this->assertCount(1, $filtered);
        $this->assertTrue($filtered->contains($category1));
        $this->assertFalse($filtered->contains($category2));
    }

    /** @test */
    public function it_can_be_transformed()
    {
        $category1 = new ServiceCategory(['name' => 'Category 1']);
        $category2 = new ServiceCategory(['name' => 'Category 2']);
        $collection = new ServiceCategoryCollection([$category1, $category2]);

        $transformed = $collection->map(function ($category) {
            return strtoupper($category->name);
        });

        $this->assertEquals(['CATEGORY 1', 'CATEGORY 2'], $transformed->toArray());
    }
}
