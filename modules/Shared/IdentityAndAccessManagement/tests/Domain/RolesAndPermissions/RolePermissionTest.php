<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\RolesAndPermissions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\RolePermission;

class RolePermissionTest extends TestCase
{
    public function testLabelReturnsCorrectLabel()
    {
        $this->assertEquals('Просмотр ролей', RolePermission::VIEW_ROLES->label());
        $this->assertEquals('Управление ролями', RolePermission::MANAGE_ROLES->label());
    }

    public function testLabelsReturnsAllLabels()
    {
        $expected = [
            'View roles' => 'Просмотр ролей',
            'Manage roles' => 'Управление ролями',
        ];

        $this->assertEquals($expected, RolePermission::labels());
    }
}
