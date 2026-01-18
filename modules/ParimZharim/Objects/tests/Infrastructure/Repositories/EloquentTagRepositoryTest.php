<?php

namespace Modules\ParimZharim\Objects\Tests\Infrastructure\Repositories;

use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentTagRepository;
use Modules\ParimZharim\Objects\Tests\TestCase;

class EloquentTagRepositoryTest extends TestCase
{
    private EloquentTagRepository $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = new EloquentTagRepository();

        // Создаем ServiceObjects
        $serviceObject1 = ServiceObject::factory()->create();
        $serviceObject2 = ServiceObject::factory()->create();

        // Создаем теги
        $visibleTag = Tag::factory()->create([
            'name' => 'Visible Tag',
            'is_visible_to_customers' => true
        ]);

        $invisibleTag = Tag::factory()->create([
            'name' => 'Invisible Tag',
            'is_visible_to_customers' => false
        ]);

        // Привязываем теги к ServiceObjects
        $serviceObject1->tags()->attach($visibleTag->id);
        $serviceObject2->tags()->attach($invisibleTag->id);
    }

    public function testGetUsableTagsReturnsOnlyVisibleTags()
    {
        // Выполняем метод и проверяем его результат
        $result = $this->tagRepository->getUsableTags();

        $this->assertInstanceOf(TagCollection::class, $result, "The returned object should be an instance of TagCollection.");
        $this->assertCount(1, $result->all(), "There should be only one visible tag in the collection.");
        $this->assertEquals('Visible Tag', $result->first()->name, "The visible tag's name should be 'Visible Tag'.");
    }
}
  
       