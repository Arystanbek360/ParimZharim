<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Tests\Domain\RolesAndPermissions;

use Modules\Shared\Profile\Domain\RolesAndPermissions\ProfilePermission;
use Modules\Shared\Core\Tests\TestCase;

class ProfilePermissionTest extends TestCase
{
    /**
     * Тестирование метода label() для всех значений перечисления.
     */
    public function testLabelMethod()
    {
        $this->assertEquals('View profiles', ProfilePermission::VIEW_PROFILES->label());
        $this->assertEquals('Manage profiles', ProfilePermission::MANAGE_PROFILES->label());
    }

    /**
     * Тестирование метода description() для всех значений перечисления.
     */
    public function testDescriptionMethod()
    {
        $this->assertEquals('Просмотр профилей', ProfilePermission::VIEW_PROFILES->description());
        $this->assertEquals('Управление профилями', ProfilePermission::MANAGE_PROFILES->description());
    }

    /**
     * Тестирование метода labels() для генерации ассоциативного массива меток.
     */
    public function testLabelsMethod()
    {
        $expected = [
            'View profiles' => 'View profiles',
            'Manage profiles' => 'Manage profiles',
        ];

        $this->assertEquals($expected, ProfilePermission::labels());
    }

    /**
     * Тестирование, что все enum случаи присутствуют в методах labels() и descriptions().
     */
    public function testAllEnumCasesPresentInLabelsAndDescriptions()
    {
        foreach (ProfilePermission::cases() as $case) {
            $this->assertArrayHasKey($case->value, ProfilePermission::labels());
        }
    }
}
