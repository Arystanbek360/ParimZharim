<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_category()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'is_visible_to_customers' => true,
        ]);

        $this->assertDatabaseHas('objects_categories', [
            'id' => $category->id,
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'is_visible_to_customers' => true,
        ]);
    }

    /** @test */
    public function it_updates_a_category()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'is_visible_to_customers' => true,
        ]);

        $category->update([
            'name' => 'Updated Category',
            'description' => 'This is an updated test category',
        ]);

        $this->assertDatabaseHas('objects_categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'description' => 'This is an updated test category',
        ]);
    }

    /** @test */
    public function it_soft_deletes_a_category()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'is_visible_to_customers' => true,
        ]);

        $category->delete();

        $this->assertSoftDeleted('objects_categories', [
            'id' => $category->id,
        ]);
    }

    /** @test */
    public function it_has_many_service_objects()
    {
        $category = Category::create([
            'name' => 'Test Category',
            'description' => 'This is a test category',
            'is_visible_to_customers' => true,
        ]);

        $serviceObject = ServiceObject::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertTrue($category->serviceObjects->contains($serviceObject));
    }

    /** @test */
    public function it_creates_a_category_with_factory()
    {
        $category = Category::factory()->create();

        $this->assertDatabaseHas('objects_categories', [
            'id' => $category->id,
        ]);
    }
}
