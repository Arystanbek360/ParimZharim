<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class PhoneVerificationCodeTest extends TestCase
{


    /** @test */
    public function it_has_correct_table_name()
    {
        $phoneVerificationCode = new PhoneVerificationCode();
        $this->assertEquals('idm_phone_verification_codes', $phoneVerificationCode->getTable());
    }

    /** @test */
    public function it_fills_correct_attributes()
    {
        $attributes = ['user_id', 'phone', 'code', 'expires_at'];
        $phoneVerificationCode = new PhoneVerificationCode();
        $this->assertEquals($attributes, $phoneVerificationCode->getFillable());
    }

    /** @test */
    public function it_hides_expected_attributes()
    {
        $phoneVerificationCode = new PhoneVerificationCode();
        $this->assertContains('code', $phoneVerificationCode->getHidden());
    }

    /** @test */
    public function it_casts_attributes_correctly()
    {
        $phoneVerificationCode = new PhoneVerificationCode(['expires_at' => now()]);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $phoneVerificationCode->expires_at);
    }


}
