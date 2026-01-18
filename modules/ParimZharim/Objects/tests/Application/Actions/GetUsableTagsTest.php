<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Application\Actions;

use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableTags;
use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;

class GetUsableTagsTest extends TestCase
{
    public function testHandleReturnsTagCollection(): void
    {
        $tagRepositoryMock = $this->createMock(TagRepository::class);

        $tagRepositoryMock->method('getUsableTags')
            ->willReturn(new TagCollection());

        $action = new GetUsableTags($tagRepositoryMock);

        $result = $action->handle();

        $this->assertInstanceOf(TagCollection::class, $result);
    }
}
