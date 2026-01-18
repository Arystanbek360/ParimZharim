<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutWebSession;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class LogoutWebSessionTest extends TestCase
{
    public function testHandleLogsOutWebSession(): void
    {
        // Create a mock for the User model
        $userMock = Mockery::mock(User::class);

        // Expect the Auth facade's logout method to be called once
        Auth::shouldReceive('logout')
            ->once();

        // Create an instance of LogoutWebSession
        $action = new LogoutWebSession();

        // Call the handle method
        $action->handle($userMock);
        
        // Verify that the expected method was called on the mock
        $this->assertTrue(true); // If no exceptions were thrown, the test is considered successful
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
