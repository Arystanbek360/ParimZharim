<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentCategoryRepository;
use Modules\ParimZharim\Objects\Tests\TestCase;

class EloquentCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private EloquentCategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new EloquentCategoryRepository();

        // Создаем категории и связанные объекты ServiceObject
        $visibleCategory = Category::factory()->create(['is_visible_to_customers' => true]);
        $invisibleCategory = Category::factory()->create(['is_visible_to_customers' => false]);

        // Создаем связанные ServiceObject для видимой категории
        ServiceObject::factory()->create(['category_id' => $visibleCategory->id]);

        // Проверка создания категорий и объектов
        error_log('Created visible category: ' . $visibleCategory->id);
        error_log('Created invisible category: ' . $invisibleCategory->id);
    }

    public function testGetUsableCategoriesReturnsOnlyVisibleCategories()
    {
        $result = $this->categoryRepository->getUsableCategories();

        // Временный вывод для диагностики
        foreach ($result as $category) {
            error_log('Category: ' . $category->id . ' Visible: ' . $category->is_visible_to_customers);
        }

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is_visible_to_customers);
    }
}
