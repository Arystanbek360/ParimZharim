<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutAllDevices;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class LogoutAllDevicesTest extends TestCase
{
    public function testHandleDeletesAllUserTokens(): void
    {
        // Create a mock for the User model
        $userMock = Mockery::mock(User::class);

        // Create mocks for the repositories
        $personalAccessTokenRepositoryMock = Mockery::mock(PersonalAccessTokenRepository::class);
        $userDeviceRepositoryMock = Mockery::mock(UserDeviceRepository::class);

        // Expect deleteAllUserTokens to be called once
        $personalAccessTokenRepositoryMock
            ->shouldReceive('deleteAllUserTokens')
            ->once()
            ->with($userMock);

        // Expect deleteAllUserDevices to be called once on UserDeviceRepository
        $userDeviceRepositoryMock
            ->shouldReceive('deleteAllUserDevices')
            ->once()
            ->with($userMock);

        // Inject mocks into LogoutAllDevices
        $action = new LogoutAllDevices($personalAccessTokenRepositoryMock, $userDeviceRepositoryMock);

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
