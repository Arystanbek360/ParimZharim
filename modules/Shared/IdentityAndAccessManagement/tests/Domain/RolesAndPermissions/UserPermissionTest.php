<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\RolesAndPermissions;

use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\UserPermission;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;

class UserPermissionTest extends TestCase
{
    public function testLabelReturnsCorrectLabel()
    {
        $this->assertEquals('Просмотр пользователей', UserPermission::VIEW_USERS->label());
        $this->assertEquals('Управление пользователями', UserPermission::MANAGE_USERS->label());
        $this->assertEquals('Управление ролями пользователей', UserPermission::MANAGE_USER_ROLES->label());
    }

    public function testLabelsReturnsAllLabels()
    {
        $expected = [
            'View users' => 'Просмотр пользователей',
            'Manage users' => 'Управление пользователями',
            'Manage user roles' => 'Управление ролями пользователей',
        ];

        $this->assertEquals($expected, UserPermission::labels());
    }
}
