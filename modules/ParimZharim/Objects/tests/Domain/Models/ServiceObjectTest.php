<?php

declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ServiceObjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_service_object()
    {
        $category = Category::factory()->create();

        $serviceObject = ServiceObject::create([
            'name' => 'Test Service Object',
            'description' => 'This is a test service object',
            'capacity' => 10,
            'is_active' => true,
            'category_id' => $category->id,
        ]);

        $this->assertDatabaseHas('objects_service_objects', [
            'id' => $serviceObject->id,
            'name' => 'Test Service Object',
            'description' => 'This is a test service object',
            'capacity' => 10,
            'is_active' => true,
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function it_belongs_to_a_category()
    {
        $category = Category::factory()->create();
        $serviceObject = ServiceObject::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->assertInstanceOf(Category::class, $serviceObject->category);
        $this->assertEquals($category->id, $serviceObject->category->id);
    }

    /** @test */
    public function it_belongs_to_many_tags()
    {
        $serviceObject = ServiceObject::factory()->create();
        $tags = Tag::factory()->count(3)->create();

        $serviceObject->tags()->attach($tags->pluck('id'));

        $this->assertCount(3, $serviceObject->tags);
        $this->assertInstanceOf(Tag::class, $serviceObject->tags->first());
    }

    /** @test */
    public function it_registers_media_collections()
    {
        $serviceObject = ServiceObject::factory()->create();

        $testImage1Name = 'test-image1.jpg';
        $testImage2Name = 'test-image2.jpg';

        $baseDir = base_path(); 
        $testImageUrl = $baseDir . '/modules/ParimZharim/Objects/tests/assets/img/test-image.jpg';
        
        $disk = config('filesystems.default');

        Storage::disk($disk)->put($testImage1Name, file_get_contents($testImageUrl));
        $fullPath1 = Storage::disk($disk)->path($testImage1Name);

        if (!Storage::disk($disk)->exists($testImage1Name)) {
            throw new \Exception('Test image not created at: ' . $fullPath1);
        }

        Storage::disk($disk)->put($testImage2Name, file_get_contents($testImageUrl));
        $fullPath2 = Storage::disk($disk)->path($testImage2Name);

        if (!Storage::disk($disk)->exists($testImage2Name)) {
            throw new \Exception('Test image not created at: ' . $fullPath2);
        }

        // Use the addMedia method with the correct full path
        $serviceObject->addMedia($fullPath1)->toMediaCollection('main');
        $serviceObject->addMedia($fullPath2)->toMediaCollection('gallery');


        // Output media information for diagnostics
        $mainMedia = $serviceObject->getMedia('main');
        $galleryMedia = $serviceObject->getMedia('gallery');


        $this->assertCount(1, $mainMedia);
        $this->assertCount(1, $galleryMedia);

        Storage::disk($disk)->delete($testImage1Name); // Delete the test file after the test
        Storage::disk($disk)->delete($testImage2Name); // Delete the test file after the test
    }

    /** @test */
    public function it_creates_a_service_object_with_factory()
    {
        $serviceObject = ServiceObject::factory()->create();

        $this->assertDatabaseHas('objects_service_objects', [
            'id' => $serviceObject->id,
        ]);
    }
}