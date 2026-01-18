<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Tests\Domain\Models;



use Modules\Natifood\Profile\Domain\Models\ProfileType;
use Modules\Shared\Profile\Tests\TestCase;

class ProfileTypeTest extends TestCase
{
    /**
     * Тестирование метода label() для всех значений перечисления.
     */
    public function testLabelMethod()
    {
        $this->assertEquals('Клиент', ProfileType::CUSTOMER->label());
        $this->assertEquals('Сотрудник', ProfileType::EMPLOYEE->label());
    }

    /**
     * Тестирование метода labels() для генерации ассоциативного массива меток.
     */
    public function testLabelsMethod()
    {
        $expected = [
            'Customer' => 'Клиент',
            'Employee' => 'Сотрудник',
        ];

        $this->assertEquals($expected, ProfileType::labels());
    }

    /**
     * Тестирование, что все enum случаи присутствуют в методе labels().
     */
    public function testAllEnumCasesPresentInLabels()
    {
        foreach (ProfileType::cases() as $case) {
            $this->assertArrayHasKey($case->value, ProfileType::labels());
        }
    }
}
