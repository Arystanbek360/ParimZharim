<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestAuthenticationPhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;
use Random\RandomException;

class RequestAuthenticationPhoneVerificationCodeForUserTest extends TestCase
{
    // protected function setUp(): void
    // {
    //     parent::setUp();
    // }

    // public function testHandleSendsVerificationCodeIfPhoneIsValid(): void
    // {
    //     $user = Mockery::mock(User::class);
    //     $phone = '+1234567890';

    //     $validatorMock = Mockery::mock('overload:ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($phone)
    //         ->andReturnTrue();

    //     $senderMock = Mockery::mock('overload:SendPhoneVerificationCodeForUser');
    //     $senderMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $senderMock->shouldReceive('handle')
    //         ->with($user, $phone)
    //         ->andReturnTrue();

    //     $action = new RequestAuthenticationPhoneVerificationCodeForUser();

    //     $action->handle($user, $phone);

    //     $this->assertTrue(true);
    // }

    // public function testHandleThrowsInvalidInputDataForInvalidPhone(): void
    // {
    //     $this->expectException(InvalidInputData::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = 'invalid_phone_number';

    //     $validatorMock = Mockery::mock('overload:ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($phone)
    //         ->andThrow(InvalidInputData::class);

    //     $action = new RequestAuthenticationPhoneVerificationCodeForUser();

    //     $action->handle($user, $phone);
    // }

    // public function testHandleThrowsRandomException(): void
    // {
    //     $this->expectException(RandomException::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = '+1234567890';

    //     $validatorMock = Mockery::mock('overload:ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($phone)
    //         ->andReturnTrue();

    //     $senderMock = Mockery::mock('overload:SendPhoneVerificationCodeForUser');
    //     $senderMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $senderMock->shouldReceive('handle')
    //         ->with($user, $phone)
    //         ->andThrow(RandomException::class);

    //     $action = new RequestAuthenticationPhoneVerificationCodeForUser();

    //     $action->handle($user, $phone);
    // }

    // public function testHandleThrowsPhoneVerificationCodeRateLimitError(): void
    // {
    //     $this->expectException(PhoneVerificationCodeRateLimitError::class);

    //     $user = Mockery::mock(User::class);
    //     $phone = '+1234567890';

    //     $validatorMock = Mockery::mock('overload:ValidateAuthenticationPhoneNumber');
    //     $validatorMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $validatorMock->shouldReceive('handle')
    //         ->with($phone)
    //         ->andReturnTrue();

    //     $senderMock = Mockery::mock('overload:SendPhoneVerificationCodeForUser');
    //     $senderMock->shouldReceive('make')
    //         ->andReturnSelf();
    //     $senderMock->shouldReceive('handle')
    //         ->with($user, $phone)
    //         ->andThrow(PhoneVerificationCodeRateLimitError::class);

    //     $action = new RequestAuthenticationPhoneVerificationCodeForUser();

    //     $action->handle($user, $phone);
    // }
    // protected function tearDown(): void
    // {
    //     Mockery::close();
    //     parent::tearDown();
    // }
}
