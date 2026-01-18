<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutDevice;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class LogoutDeviceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHandleDeletesDeviceToken(): void
    {
        $user = Mockery::mock(User::class);
        $device_id = 'device_123';

        $personalAccessTokenRepositoryMock = Mockery::mock(PersonalAccessTokenRepository::class);
        $userDeviceRepositoryMock = Mockery::mock(UserDeviceRepository::class);

        // Ожидание вызова deleteByName на PersonalAccessTokenRepository
        $personalAccessTokenRepositoryMock->shouldReceive('deleteByName')
            ->once()
            ->with($user, $device_id);

        // Ожидание вызова deleteUserDevice на UserDeviceRepository
        $userDeviceRepositoryMock->shouldReceive('deleteUserDevice')
            ->once()
            ->with($user, $device_id);

        // Устанавливаем моки в контейнер приложения
        $this->app->instance(PersonalAccessTokenRepository::class, $personalAccessTokenRepositoryMock);
        $this->app->instance(UserDeviceRepository::class, $userDeviceRepositoryMock);

        $action = new LogoutDevice($personalAccessTokenRepositoryMock, $userDeviceRepositoryMock);

        $action->handle($user, $device_id);

        $this->assertTrue(true); // Тест успешен, если нет исключений
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
