<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Application\Actions;

use Modules\ParimZharim\Objects\Tests\TestCase;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableCategories;
use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;
use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;

class GetUsableCategoriesTest extends TestCase
{
    public function testHandleReturnsCategoryCollection(): void
    {
        $categoryRepositoryMock = $this->createMock(CategoryRepository::class);

        $categoryRepositoryMock->method('getUsableCategories')
            ->willReturn(new CategoryCollection());

        $action = new GetUsableCategories($categoryRepositoryMock);

        $result = $action->handle();

        $this->assertInstanceOf(CategoryCollection::class, $result);
    }
}
