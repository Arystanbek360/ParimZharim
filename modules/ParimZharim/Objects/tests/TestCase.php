<?php

namespace Modules\ParimZharim\Objects\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\ParimZharim\Objects\ObjectsModuleProvider;
use Modules\Shared\Core\Tests\BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Test service bindings in the module provider.
     */
    public function testServiceBindings()
    {
        $provider = new ObjectsModuleProvider($this->app);
        $provider->register();
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
