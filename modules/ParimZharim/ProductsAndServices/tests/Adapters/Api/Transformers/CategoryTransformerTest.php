<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Adapters\Api\Transformers;

use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\CategoryTransformer;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCategory;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class CategoryTransformerTest extends TestCase
{
    private CategoryTransformer $transformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transformer = new CategoryTransformer();
    }

    // В тесте
public function testTransformProductCategory(): void
{
    $productCategory = new ProductCategory();
    $productCategory->forceFill([
        'id' => 1,
        'name' => 'Electronics'
    ]);

    $transformed = $this->transformer->transform($productCategory);

    $this->assertIsArray($transformed);
    $this->assertEquals(1, $transformed['id']);
    $this->assertEquals('Electronics', $transformed['name']);
}

public function testTransformServiceCategory(): void
{
    $serviceCategory = new ServiceCategory();
    $serviceCategory->forceFill([
        'id' => 2,
        'name' => 'Consulting'
    ]);

    $transformed = $this->transformer->transform($serviceCategory);

    $this->assertIsArray($transformed);
    $this->assertEquals(2, $transformed['id']);
    $this->assertEquals('Consulting', $transformed['name']);
}

}
