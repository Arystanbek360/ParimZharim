<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Adapters\Api\Transformers;

use Modules\ParimZharim\Objects\Adapters\Api\Transformers\CategoryTransformer;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class CategoryTransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_category_correctly()
    {
        // Устанавливаем мок для Storage
        Storage::shouldReceive('disk->url')
            ->with('path/to/image.jpg')
            ->andReturn('http://localhost/storage/path/to/image.jpg');

        // Создаем объект категории
        $category = new Category();
        $category->id = 1;
        $category->name = 'Category 1';
        $category->image = 'path/to/image.jpg';

        // Создаем трансформер
        $transformer = new CategoryTransformer();

        // Применяем трансформацию
        $transformedData = $transformer->transform($category);

        // Проверяем результат
        $this->assertEquals([
            'type' => 1,
            'name' => 'Category 1',
            'photo' => 'http://localhost/storage/path/to/image.jpg',
        ], $transformedData);
    }

    /** @test */
    public function it_transforms_category_without_image_correctly()
    {
        // Создаем объект категории без изображения
        $category = new Category();
        $category->id = 2;
        $category->name = 'Category 2';
        $category->image = null;

        // Создаем трансформер
        $transformer = new CategoryTransformer();

        // Применяем трансформацию
        $transformedData = $transformer->transform($category);

        // Проверяем результат
        $this->assertEquals([
            'type' => 2,
            'name' => 'Category 2',
            'photo' => null,
        ], $transformedData);
    }
}
