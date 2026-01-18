<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Infrastructure\Repositories\EloquentServiceCategoryRepository;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;

class EloquentServiceCategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_only_usable_service_categories()
{
    // Arrange
    $repository = new EloquentServiceCategoryRepository();

    $category = ServiceCategory::factory()->create([
        'is_visible_to_customers' => true
    ]);

    // Создаем услуги с использованием ServiceFactory, которая автоматически устанавливает price
    Service::factory()->count(2)->create([
        'service_category_id' => $category->id,
        'is_active' => true
    ]);

    // Act
    $categories = $repository->getUsableServiceCategories();

    // Assert
    $this->assertCount(1, $categories);
    $this->assertTrue($categories->first()->is_visible_to_customers);
    $this->assertNotEmpty($categories->first()->services);
}}
