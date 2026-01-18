<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ProductRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ProductCollection;

class ProductRepositoryTest extends TestCase
{
    private $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = $this->createMock(ProductRepository::class);
    }

    public function testGetProductsByCategoryReturnsProductCollection()
    {
        $categoryID = 1;
        $expectedCollection = new ProductCollection();
        $this->productRepository->method('getProductsByCategory')->with($categoryID)->willReturn($expectedCollection);

        $result = $this->productRepository->getProductsByCategory($categoryID);
        $this->assertInstanceOf(ProductCollection::class, $result, "The returned object should be an instance of ProductCollection.");
    }

    public function testGetAllProductsReturnsProductCollection()
    {
        $expectedCollection = new ProductCollection();
        $this->productRepository->method('getAllProducts')->willReturn($expectedCollection);

        $result = $this->productRepository->getAllProducts();
        $this->assertInstanceOf(ProductCollection::class, $result, "The returned object should be an instance of ProductCollection.");
    }
}
