<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\DTO;

use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class PhoneAuthenticationRequestDataTest extends TestCase
{
    public function testDTOCreation()
    {
        $phone = '+1234567890';
        $code = 123456;
        $device_id = 'test-device-id';

        $dto = new PhoneAuthenticationRequestData($phone, $code, $device_id);

        $this->assertEquals($phone, $dto->phone);
        $this->assertEquals($code, $dto->code);
        $this->assertEquals($device_id, $dto->device_id);
    }
}
