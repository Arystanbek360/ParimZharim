<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Models;

use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\EnumResolver;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class PermissionTest extends TestCase
{
    /** @test */
    public function it_returns_correct_label_for_permission_name()
    {
        // Создаем реальный объект EnumResolver
        $enums = ['edit_posts' => 'edit_posts']; // Используем правильную структуру для данных
        $enumResolver = new EnumResolver($enums);

        // Мокируем EnumResolver
        $enumResolverMock = Mockery::mock(EnumResolver::class);
        $enumResolverMock->shouldReceive('labelFromValue')
                         ->with('edit_posts')
                         ->andReturn('edit_posts');

        // Регистрируем мок в контейнере приложения
        $this->app->instance(EnumResolver::class, $enumResolverMock);

        // Создаем экземпляр Permission и вызываем метод getLabelAttribute
        $permission = new Permission(['name' => 'edit_posts']);
        $label = $permission->getLabelAttribute();

        // Проверяем возвращаемое значение
        $this->assertEquals('edit_posts', $label);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
