<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_tag()
    {
        $tag = Tag::create([
            'name' => 'Test Tag',
        ]);

        $this->assertDatabaseHas('objects_tags', [
            'id' => $tag->id,
            'name' => 'Test Tag',
        ]);
    }

    /** @test */
    public function it_belongs_to_many_service_objects()
    {
        $tag = Tag::factory()->create();
        $serviceObjects = ServiceObject::factory()->count(3)->create();

        $tag->serviceObjects()->attach($serviceObjects->pluck('id'));

        $this->assertCount(3, $tag->serviceObjects);
        $this->assertInstanceOf(ServiceObject::class, $tag->serviceObjects->first());
    }

    /** @test */
    public function it_creates_a_tag_with_factory()
    {
        $tag = Tag::factory()->create();

        $this->assertDatabaseHas('objects_tags', [
            'id' => $tag->id,
        ]);
    }
}
