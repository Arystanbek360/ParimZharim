<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\RolesAndPermissions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;

class RolesTest extends TestCase
{
    public function testLabelReturnsCorrectLabel()
    {
        $this->assertEquals('Супер администратор', Roles::SUPER_ADMIN->label());
        $this->assertEquals('Пользователь административной панели', Roles::ADMIN->label());
    }

    public function testLabelsReturnsAllLabels()
    {
        $expected = [
            'Super admin' => 'Супер администратор',
            'Admin' => 'Пользователь административной панели',
        ];

        $this->assertEquals($expected, Roles::labels());
    }
}
