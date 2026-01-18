<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetUserProfileByEmail;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class GetUserProfileByEmailTest extends TestCase
{
    public function testHandleReturnsUserProfile(): void
    {
        $email = 'test@example.com';
        
        // Create a mock for the UserRepository
        $userRepositoryMock = Mockery::mock(UserRepository::class);
        
        // Create a mock for the User model
        $userMock = Mockery::mock(User::class);
        
        // Expect the findByEmail method to be called once with the specified email
        $userRepositoryMock
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturn($userMock);

        // Create an instance of GetUserProfileByEmail with the mocked repository
        $action = new GetUserProfileByEmail($userRepositoryMock);

        // Call the handle method and assert the result
        $result = $action->handle($email);
        $this->assertSame($userMock, $result);
    }
    
    public function testHandleReturnsNullWhenUserNotFound(): void
    {
        $email = 'notfound@example.com';
        
        // Create a mock for the UserRepository
        $userRepositoryMock = Mockery::mock(UserRepository::class);
        
        // Expect the findByEmail method to be called once with the specified email and return null
        $userRepositoryMock
            ->shouldReceive('findByEmail')
            ->once()
            ->with($email)
            ->andReturnNull();

        // Create an instance of GetUserProfileByEmail with the mocked repository
        $action = new GetUserProfileByEmail($userRepositoryMock);

        // Call the handle method and assert the result
        $result = $action->handle($email);
        $this->assertNull($result);
    }
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
