<?php

namespace Modules\ParimZharim\Objects\Tests\Domain\Repositories;

use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;
use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;

class CategoryRepositoryTest extends TestCase
{
    private $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Здесь мы создаем мок реализации CategoryRepository
        $this->categoryRepository = $this->createMock(CategoryRepository::class);
    }

    public function testGetUsableCategoriesReturnsCategoryCollection()
    {
        // Подготовка возвращаемого значения метода getUsableCategories
        $expectedCollection = new CategoryCollection();
        $this->categoryRepository->method('getUsableCategories')->willReturn($expectedCollection);

        // Вызов метода getUsableCategories и проверка результата
        $result = $this->categoryRepository->getUsableCategories();
        $this->assertInstanceOf(CategoryCollection::class, $result, "The returned object should be an instance of CategoryCollection.");
    }
}
