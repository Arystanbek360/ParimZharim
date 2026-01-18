<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetUserProfileByPhone;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class GetUserProfileByPhoneTest extends TestCase
{
    public function testHandleReturnsUserProfile(): void
    {
        $phone = '1234567890';

        // Create a mock for the UserRepository
        $userRepositoryMock = Mockery::mock(UserRepository::class);

        // Create a mock for the User model
        $userMock = Mockery::mock(User::class);

        // Expect the findByPhone method to be called once with the specified phone
        $userRepositoryMock
            ->shouldReceive('findByPhone')
            ->once()
            ->with($phone)
            ->andReturn($userMock);

        // Create an instance of GetUserProfileByPhone with the mocked repository
        $action = new GetUserProfileByPhone($userRepositoryMock);

        // Call the handle method and assert the result
        $result = $action->handle($phone);
        $this->assertSame($userMock, $result);
    }

    public function testHandleReturnsNullWhenUserNotFound(): void
    {
        $phone = '0987654321';

        // Create a mock for the UserRepository
        $userRepositoryMock = Mockery::mock(UserRepository::class);

        // Expect the findByPhone method to be called once with the specified phone and return null
        $userRepositoryMock
            ->shouldReceive('findByPhone')
            ->once()
            ->with($phone)
            ->andReturnNull();

        // Create an instance of GetUserProfileByPhone with the mocked repository
        $action = new GetUserProfileByPhone($userRepositoryMock);

        // Call the handle method and assert the result
        $result = $action->handle($phone);
        $this->assertNull($result);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
