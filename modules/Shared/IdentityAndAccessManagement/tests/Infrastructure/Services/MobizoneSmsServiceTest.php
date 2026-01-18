<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Infrastructure\Services;

use Illuminate\Support\Facades\Http;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;
use Modules\Shared\IdentityAndAccessManagement\Infrastructure\Services\MobizoneSmsService;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class MobizoneSmsServiceTest extends TestCase
{
    private $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->smsService = new MobizoneSmsService();
    }

    public function testSendSmsFailsDueToApiError()
    {
        config()->set('app.idm_mobizone_token', 'valid_token'); // Установка правильных учетных данных

        Http::fake([
            'api.mobizon.kz/*' => Http::response(['code' => 1, 'message' => 'Error'], 200)
        ]);

        $this->expectException(SMSServiceCommunicationError::class);

        $this->smsService->send('+1234567890', 'Test message');
    }

    public function testSendSmsFailsDueToInvalidCredentials()
    {
        config()->set('app.idm_mobizone_token', null);

        $this->expectException(InvalidSMSServiceCredentials::class);

        $this->smsService->send('+1234567890', 'Test message');
    }

}
