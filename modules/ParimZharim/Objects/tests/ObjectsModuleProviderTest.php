<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\Objects\ObjectsModuleProvider;
use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentCategoryRepository;
use Modules\ParimZharim\Objects\Infrastructure\Repositories\EloquentTagRepository;

class ObjectsModuleProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test service bindings in the module provider.
     */
    public function testServiceBindings()
    {
        $provider = new ObjectsModuleProvider($this->app);
        $provider->register();

        $this->assertInstanceOf(
            EloquentCategoryRepository::class,
            $this->app->make(CategoryRepository::class),
            'CategoryRepository should be bound to EloquentCategoryRepository'
        );

        $this->assertInstanceOf(
            EloquentTagRepository::class,
            $this->app->make(TagRepository::class),
            'TagRepository should be bound to EloquentTagRepository'
        );
    }

    /**
     * Test the loading of migrations and routes.
     */
    public function testMigrationsAndRoutesLoading()
    {
        $provider = new ObjectsModuleProvider($this->app);
        $provider->boot();

        // Получаем зарегистрированные пути миграций
        $migrations = $this->app['migrator']->paths();

        // Рассчитываем относительный путь к миграциям
        $expectedPath = realpath(__DIR__ . '/../database/migrations');

        // Преобразуем пути к миграциям в относительные
        $migrations = array_map('realpath', $migrations);

        // Проверяем, зарегистрирован ли ожидаемый путь миграций
        $this->assertTrue(in_array($expectedPath, $migrations), 'Migrations should be loaded');
    }
}
