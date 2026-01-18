<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\DTO;

use Modules\Shared\IdentityAndAccessManagement\Application\DTO\EmailAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class EmailAuthenticationRequestDataTest extends TestCase
{
    public function testDTOCreation()
    {
        $email = 'test@example.com';
        $password = 'password123';

        // Создание DTO
        $dto = new EmailAuthenticationRequestData($email, $password);

        // Проверка значений
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($password, $dto->password);
    }
}
