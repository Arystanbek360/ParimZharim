<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\Actions;

use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ValidateAuthenticationPhoneNumber;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class ValidateAuthenticationPhoneNumberTest extends TestCase
{
    // public function testValidatesCorrectPhoneNumber(): void
    // {
    //     $phone = '+123456789012';
    //     $validator = new ValidateAuthenticationPhoneNumber();
        
    //     try {
    //         $validator->handle($phone);
    //         $this->assertTrue(true);
    //     } catch (InvalidInputData $e) {
    //         $this->fail("Exception should not be thrown for a valid phone number");
    //     }
    // }

    // public function testThrowsExceptionForInvalidPhoneNumber(): void
    // {
    //     $this->expectException(InvalidInputData::class);
    //     $this->expectExceptionMessage("Invalid phone number");

    //     $phone = '12345';
    //     $validator = new ValidateAuthenticationPhoneNumber();
    //     $validator->handle($phone);
    // }

    // public function testThrowsExceptionForIncorrectFormatPhoneNumber(): void
    // {
    //     $this->expectException(InvalidInputData::class);
    //     $this->expectExceptionMessage("Invalid phone number");

    //     $phone = '+12-3456789012';
    //     $validator = new ValidateAuthenticationPhoneNumber();
    //     $validator->handle($phone);
    // }

    // public function testThrowsExceptionForTooShortPhoneNumber(): void
    // {
    //     $this->expectException(InvalidInputData::class);
    //     $this->expectExceptionMessage("Invalid phone number");

    //     $phone = '+12345';
    //     $validator = new ValidateAuthenticationPhoneNumber();
    //     $validator->handle($phone);
    // }

    // public function testThrowsExceptionForTooLongPhoneNumber(): void
    // {
    //     $this->expectException(InvalidInputData::class);
    //     $this->expectExceptionMessage("Invalid phone number");

    //     $phone = '+12345678901234567890';
    //     $validator = new ValidateAuthenticationPhoneNumber();
    //     $validator->handle($phone);
    // }
}
