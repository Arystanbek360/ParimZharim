<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_many_services()
    {
        $category = ServiceCategory::factory()->create();
        $service1 = Service::factory()->create(['service_category_id' => $category->id]);
        $service2 = Service::factory()->create(['service_category_id' => $category->id]);

        $this->assertInstanceOf(Service::class, $category->services->first());
        $this->assertCount(2, $category->services);
    }

    /** @test */
    public function it_can_create_service_category_with_factory()
    {
        $category = ServiceCategory::factory()->create([
            'name' => 'Test Category',
            'is_visible_to_customers' => true,
        ]);

        $this->assertDatabaseHas('products_and_services_service_categories', [
            'name' => 'Test Category',
            'is_visible_to_customers' => true,
        ]);
    }

    /** @test */
    public function it_soft_deletes_service_category()
    {
        $category = ServiceCategory::factory()->create();

        $category->delete();

        $this->assertSoftDeleted($category);
    }
}
