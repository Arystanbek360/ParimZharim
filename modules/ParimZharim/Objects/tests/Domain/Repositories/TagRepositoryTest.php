<?php

namespace Modules\ParimZharim\Objects\Tests\Domain\Repositories;

use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;
use Modules\ParimZharim\Objects\Domain\Models\TagCollection;

class TagRepositoryTest extends TestCase
{
    private $tagRepository;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем мок реализации TagRepository
        $this->tagRepository = $this->createMock(TagRepository::class);
    }

    public function testGetUsableTagsReturnsTagCollection()
    {
        // Подготовка возвращаемого значения метода getUsableTags
        $expectedCollection = new TagCollection();
        $this->tagRepository->method('getUsableTags')->willReturn($expectedCollection);

        // Вызов метода getUsableTags и проверка результата
        $result = $this->tagRepository->getUsableTags();
        $this->assertInstanceOf(TagCollection::class, $result, "The returned object should be an instance of TagCollection.");
    }
}
