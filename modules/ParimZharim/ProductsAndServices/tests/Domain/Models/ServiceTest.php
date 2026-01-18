<?php

namespace Modules\ParimZharim\ProductsAndServices\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\ServiceCategory;
use Modules\ParimZharim\ProductsAndServices\Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_belongs_to_a_service_category()
    {
        $category = ServiceCategory::factory()->create();
        $service = Service::factory()->create(['service_category_id' => $category->id]);

        $this->assertInstanceOf(ServiceCategory::class, $service->serviceCategory);
        $this->assertEquals($category->id, $service->serviceCategory->id);
    }

    /** @test */
    public function it_can_create_service_with_factory()
    {
        $service = Service::factory()->create([
            'name' => 'Test Service',
            'description' => 'Test Description',
            'price' => 99.99,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('products_and_services_services', [
            'name' => 'Test Service',
            'description' => 'Test Description',
            'price' => 99.99,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function it_soft_deletes_service()
    {
        $service = Service::factory()->create();

        $service->delete();

        $this->assertSoftDeleted($service);
    }
}
