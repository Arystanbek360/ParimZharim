<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidatePhoneCanRequestAuthenticationPhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Mockery;
use Illuminate\Support\Carbon;

class ValidatePhoneCanRequestAuthenticationPhoneVerificationCodeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testHandleDoesNotThrowErrorWhenLimitsNotExceeded(): void
    {
        $phone = '+1234567890';

        $repositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
        $repositoryMock->shouldReceive('findLastByPhone')
            ->once()
            ->with($phone)
            ->andReturnNull();
        $repositoryMock->shouldReceive('countAllByPhonePerDay')
            ->once()
            ->with($phone)
            ->andReturn(0);
        $repositoryMock->shouldReceive('countAllPerDay')
            ->once()
            ->andReturn(0);

        $this->app->instance(PhoneVerificationCodeRepository::class, $repositoryMock);

        $action = new ValidatePhoneCanRequestAuthenticationPhoneVerificationCode($repositoryMock);

        $action->handle($phone);

        $this->assertTrue(true);
    }

    public function testHandleThrowsErrorWhenIntervalLimitExceeded(): void
    {
        $this->expectException(PhoneVerificationCodeRateLimitError::class);
        $this->expectExceptionMessage('You can only send one PHONE_VERIFICATION_CODE every ');

        $phone = '+1234567890';

        $lastCodeEntry = Mockery::mock(PhoneVerificationCode::class);
        $lastCodeEntry->shouldReceive('getAttribute')
            ->with('created_at')
            ->andReturn(Carbon::now()->subSeconds(30));

        $repositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
        $repositoryMock->shouldReceive('findLastByPhone')
            ->once()
            ->with($phone)
            ->andReturn($lastCodeEntry);
        $repositoryMock->shouldReceive('countAllByPhonePerDay')
            ->never();
        $repositoryMock->shouldReceive('countAllPerDay')
            ->never();

        $this->app->instance(PhoneVerificationCodeRepository::class, $repositoryMock);

        $action = new ValidatePhoneCanRequestAuthenticationPhoneVerificationCode($repositoryMock);

        $action->handle($phone);
    }

    public function testHandleThrowsErrorWhenDailyLimitExceededForPhone(): void
    {
        $this->expectException(PhoneVerificationCodeRateLimitError::class);
        $this->expectExceptionMessage('You can send only ');

        $phone = '+1234567890';

        $repositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
        $repositoryMock->shouldReceive('findLastByPhone')
            ->once()
            ->with($phone)
            ->andReturnNull();
        $repositoryMock->shouldReceive('countAllByPhonePerDay')
            ->once()
            ->with($phone)
            ->andReturn(config('app.idm_phone_verification_code_rate_limit_per_user_per_day') + 1);
        $repositoryMock->shouldReceive('countAllPerDay')
            ->never();

        $this->app->instance(PhoneVerificationCodeRepository::class, $repositoryMock);

        $action = new ValidatePhoneCanRequestAuthenticationPhoneVerificationCode($repositoryMock);

        $action->handle($phone);
    }

    public function testHandleThrowsErrorWhenDailyLimitExceededForAllPhones(): void
    {
        $this->expectException(PhoneVerificationCodeRateLimitError::class);
        $this->expectExceptionMessage('You have a limited number of SMS messages.');

        $phone = '+1234567890';

        $repositoryMock = Mockery::mock(PhoneVerificationCodeRepository::class);
        $repositoryMock->shouldReceive('findLastByPhone')
            ->once()
            ->with($phone)
            ->andReturnNull();
        $repositoryMock->shouldReceive('countAllByPhonePerDay')
            ->once()
            ->with($phone)
            ->andReturn(0);
        $repositoryMock->shouldReceive('countAllPerDay')
            ->once()
            ->andReturn(config('app.idm_phone_verification_code_rate_limit_per_all_users_per_day') + 1);

        $this->app->instance(PhoneVerificationCodeRepository::class, $repositoryMock);

        $action = new ValidatePhoneCanRequestAuthenticationPhoneVerificationCode($repositoryMock);

        $action->handle($phone);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
