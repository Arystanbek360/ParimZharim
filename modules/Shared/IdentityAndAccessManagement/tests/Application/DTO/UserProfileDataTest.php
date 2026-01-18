<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\DTO;

use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class UserProfileDataTest extends TestCase
{
    public function testDTOCreationWithAllFields()
    {
        $phone = '+1234567890';
        $email = 'test@example.com';
        $name = 'Test User';
        $password = 'password123';

        // Создание DTO
        $dto = new UserProfileData($phone, $email, $name, $password);

        // Проверка значений
        $this->assertEquals($phone, $dto->phone);
        $this->assertEquals($email, $dto->email);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($password, $dto->password);
    }

    public function testDTOCreationWithSomeFields()
    {
        $phone = '+1234567890';
        $email = 'test@example.com';

        // Создание DTO
        $dto = new UserProfileData($phone, $email);

        // Проверка значений
        $this->assertEquals($phone, $dto->phone);
        $this->assertEquals($email, $dto->email);
        $this->assertNull($dto->name);
        $this->assertNull($dto->password);
    }

    public function testDTOCreationWithNoFields()
    {
        // Создание DTO
        $dto = new UserProfileData();

        // Проверка значений
        $this->assertNull($dto->phone);
        $this->assertNull($dto->email);
        $this->assertNull($dto->name);
        $this->assertNull($dto->password);
    }
}
