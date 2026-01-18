<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Application\DTO;

use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class PhoneChangeRequestDataTest extends TestCase
{
    public function testDTOCreation()
    {
        // Создание пользователя
        $user = User::factory()->create();
        $phone = '+72345678090';
        $code = 666666;

        // Создание DTO
        $dto = new PhoneChangeRequestData($user, $phone, $code);

        // Проверка значений
        $this->assertEquals($user, $dto->user);
        $this->assertEquals($phone, $dto->phone);
        $this->assertEquals($code, $dto->code);
    }
}
