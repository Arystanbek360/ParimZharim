<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\DeleteOldPhoneVerificationCodes;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;

class DeleteOldPhoneVerificationCodesTest extends TestCase
{
    public function testHandleDeletesOldPhoneVerificationCodes(): void
    {
        // Create a mock for the PhoneVerificationCodeRepository
        $phoneVerificationCodeRepositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
        
        // Expect the deleteOlderThan method to be called once with the specified number of days
        $phoneVerificationCodeRepositoryMock
            ->shouldReceive('deleteOlderThan')
            ->once()
            ->with(60);

        // Create an instance of DeleteOldPhoneVerificationCodes with the mocked repository
        $action = new DeleteOldPhoneVerificationCodes($phoneVerificationCodeRepositoryMock);

        // Call the handle method
        $action->handle(60);
        
        // Verify that the expected method was called on the mock
        $this->assertTrue(true); // If no exceptions were thrown, the test is considered successful
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

}
