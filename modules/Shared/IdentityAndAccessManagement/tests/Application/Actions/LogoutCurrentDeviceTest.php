<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutAllDevices;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutCurrentDevice;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class LogoutCurrentDeviceTest extends TestCase
{
    public function testHandleDeletesCurrentUserToken(): void
    {
        // Create a mock for the User model
        $userMock = Mockery::mock(User::class);

        // Create mocks for the repositories
        $personalAccessTokenRepositoryMock = Mockery::mock(PersonalAccessTokenRepository::class);
        $userDeviceRepositoryMock = Mockery::mock(UserDeviceRepository::class);

        // Expect deleteCurrentUserToken to be called once
        $personalAccessTokenRepositoryMock
            ->shouldReceive('deleteCurrentUserToken')
            ->once()
            ->with($userMock);

        // Expect deleteCurrentUserDevice to be called once on UserDeviceRepository
        $userDeviceRepositoryMock
            ->shouldReceive('deleteCurrentUserDevice')
            ->once()
            ->with($userMock);

        // Inject mocks into LogoutCurrentDevice
        $action = new LogoutCurrentDevice($personalAccessTokenRepositoryMock, $userDeviceRepositoryMock);

        // Call handle
        $action->handle($userMock);

        $this->assertTrue(true); // Passes if no exceptions are thrown
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
