<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Infrastructure\Repositories;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Domain\Repositories\TagRepository;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentTagRepository;
use Modules\Shared\Documents\Tests\TestCase;

class EloquentTagRepositoryTest extends TestCase
{


    private TagRepository $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tagRepository = new EloquentTagRepository();
    }

    public function testSaveTagSuccessfully(): void
    {
        $tag = Tag::factory()->make(['name' => 'TestTag']);

        $this->tagRepository->saveTag($tag);

        $this->assertDatabaseHas('documents_tags', ['name' => 'TestTag']);
    }

    public function testGetTagByIdsReturnsCorrectTags(): void
    {
        $tag1 = Tag::factory()->create(['name' => 'Tag1']);
        $tag2 = Tag::factory()->create(['name' => 'Tag2']);

        $tags = $this->tagRepository->getTagByIds([$tag1->id, $tag2->id]);

        $this->assertNotNull($tags);
        $this->assertCount(2, $tags);
        $this->assertEquals('Tag1', $tags->first()->name);
        $this->assertEquals('Tag2', $tags->last()->name);
    }

    public function testGetTagByIdsReturnsNullForInvalidIds(): void
    {
        $tags = $this->tagRepository->getTagByIds([9999]);
        $this->assertEmpty($tags);
    }

    public function testGetTagsWithLimitAndOffset(): void
    {
        Tag::factory()->count(10)->create();

        $tags = $this->tagRepository->getTags(5, 0);  // Получение первых 5 тегов

        $this->assertNotNull($tags);
        $this->assertCount(5, $tags);

        $tags = $this->tagRepository->getTags(5, 5);  // Получение следующих 5 тегов

        $this->assertNotNull($tags);
        $this->assertCount(5, $tags);
    }

    public function testGetTagsReturnsNullWhenNoTags(): void
    {
        $tags = $this->tagRepository->getTags();
        $this->assertEmpty($tags);
    }
}
