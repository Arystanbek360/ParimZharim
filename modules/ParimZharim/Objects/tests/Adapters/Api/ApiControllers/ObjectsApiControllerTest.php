<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Adapters\Api\ApiControllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableCategories;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableTags;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Mockery;

class ObjectsApiControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Определяем маршруты
        $this->app['router']->get('api/categories', [
            'as' => 'api.categories',
            'uses' => 'Modules\ParimZharim\Objects\Adapters\Api\ApiControllers\ObjectsApiController@getCategories'
        ]);

        $this->app['router']->get('api/tags', [
            'as' => 'api.tags',
            'uses' => 'Modules\ParimZharim\Objects\Adapters\Api\ApiControllers\ObjectsApiController@getTags'
        ]);
    }

    /** @test */
    public function it_returns_categories()
    {
        // Создаем объекты категорий
        $category1 = new Category();
        $category1->id = 1;
        $category1->name = 'Category 1';
        $category1->image = 'path/to/image1.jpg';

        $category2 = new Category();
        $category2->id = 2;
        $category2->name = 'Category 2';
        $category2->image = 'path/to/image2.jpg';

        $categories = new CategoryCollection([$category1, $category2]);

        $mock = Mockery::mock(GetUsableCategories::class);
        $mock->shouldReceive('handle')->once()->andReturn($categories);
        $this->app->instance(GetUsableCategories::class, $mock);

        // Выполняем запрос к API и логируем исключения
        try {
            $response = $this->getJson(route('api.categories'));

            // Логируем содержимое ответа для отладки
            if ($response->status() !== 200) {
                dd($response->getContent());
            }

            // Проверяем ответ
            $response->assertStatus(200);
            $response->assertJson([
                [
                    'type' => 1,
                    'name' => 'Category 1',
                    'photo' => Storage::url('path/to/image1.jpg')
                ],
                [
                    'type' => 2,
                    'name' => 'Category 2',
                    'photo' => Storage::url('path/to/image2.jpg')
                ]
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }

    /** @test */
    public function it_returns_tags()
    {
        // Создаем объекты тегов
        $tag1 = new Tag();
        $tag1->id = 1;
        $tag1->name = 'Tag 1';
        $tag1->image = 'path/to/image1.jpg';

        $tag2 = new Tag();
        $tag2->id = 2;
        $tag2->name = 'Tag 2';
        $tag2->image = 'path/to/image2.jpg';

        $tags = new TagCollection([$tag1, $tag2]);

        $mock = Mockery::mock(GetUsableTags::class);
        $mock->shouldReceive('handle')->once()->andReturn($tags);
        $this->app->instance(GetUsableTags::class, $mock);

        // Выполняем запрос к API и логируем исключения
        try {
            $response = $this->getJson(route('api.tags'));

            // Логируем содержимое ответа для отладки
            if ($response->status() !== 200) {
                dd($response->getContent());
            }

            // Проверяем ответ
            $response->assertStatus(200);
            $response->assertJson([
                [
                    'id' => 1,
                    'name' => 'Tag 1',
                    'img' => Storage::url('path/to/image1.jpg')
                ],
                [
                    'id' => 2,
                    'name' => 'Tag 2',
                    'img' => Storage::url('path/to/image2.jpg')
                ]
            ]);
        } catch (\Exception $e) {
            $this->fail($e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
