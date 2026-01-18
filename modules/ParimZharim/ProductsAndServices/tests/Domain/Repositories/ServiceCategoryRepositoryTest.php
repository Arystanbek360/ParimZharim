<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Repositories;

use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Modules\ParimZharim\ProductsAndServices\Domain\Repositories\ServiceCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategoryCollection;

class ServiceCategoryRepositoryTest extends TestCase
{
    private $serviceCategoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->serviceCategoryRepository = $this->createMock(ServiceCategoryRepository::class);
    }

    public function testGetUsableServiceCategoriesReturnsServiceCategoryCollection()
    {
        // Подготовка возвращаемого значения метода getUsableServiceCategories
        $expectedCollection = new ServiceCategoryCollection();
        $this->serviceCategoryRepository->method('getUsableServiceCategories')->willReturn($expectedCollection);

        // Вызов метода getUsableServiceCategories и проверка результата
        $result = $this->serviceCategoryRepository->getUsableServiceCategories();
        $this->assertInstanceOf(ServiceCategoryCollection::class, $result, "The returned object should be an instance of ServiceCategoryCollection.");
    }
}
