<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Adapters\Cli;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\DeleteOldPhoneVerificationCodes;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class DeleteOldPhoneVerificationCodesConsoleCommandTest extends TestCase
{


    public function testDeleteOldPhoneVerificationCodesConsoleCommand()
    {
        $this->mock(DeleteOldPhoneVerificationCodes::class, function ($mock) {
            $mock->shouldReceive('handle')->once()->andReturn(true);
        });

        $exitCode = Artisan::call('idm:delete-old-phone-verification-codes');

        $this->assertEquals(0, $exitCode);
    }
}
