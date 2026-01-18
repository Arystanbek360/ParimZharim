<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Adapters\Api\Transformers;

use Modules\ParimZharim\Objects\Adapters\Api\Transformers\TagTransformer;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class TagTransformerTest extends TestCase
{
    /** @test */
    public function it_transforms_tag_correctly()
    {
        // Устанавливаем мок для Storage
        Storage::shouldReceive('disk->url')
            ->with('path/to/image.jpg')
            ->andReturn('http://localhost/storage/path/to/image.jpg');

        // Создаем объект тега
        $tag = new Tag();
        $tag->id = 1;
        $tag->name = 'Tag 1';
        $tag->image = 'path/to/image.jpg';

        // Создаем трансформер
        $transformer = new TagTransformer();

        // Применяем трансформацию
        $transformedData = $transformer->transform($tag);

        // Проверяем результат
        $this->assertEquals([
            'id' => 1,
            'name' => 'Tag 1',
            'img' => 'http://localhost/storage/path/to/image.jpg',
        ], $transformedData);
    }

    /** @test */
    public function it_transforms_tag_without_image_correctly()
    {
        // Создаем объект тега без изображения
        $tag = new Tag();
        $tag->id = 2;
        $tag->name = 'Tag 2';
        $tag->image = null;

        // Создаем трансформер
        $transformer = new TagTransformer();

        // Применяем трансформацию
        $transformedData = $transformer->transform($tag);

        // Проверяем результат
        $this->assertEquals([
            'id' => 2,
            'name' => 'Tag 2',
            'img' => null,
        ], $transformedData);
    }
}
