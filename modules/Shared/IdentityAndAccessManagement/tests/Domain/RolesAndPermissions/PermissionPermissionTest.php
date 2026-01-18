<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\RolesAndPermissions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\PermissionPermission;

class PermissionPermissionTest extends TestCase
{
    public function testLabelReturnsCorrectLabel()
    {
        $this->assertEquals('Просмотр разрешений', PermissionPermission::VIEW_PERMISSIONS->label());
    }

    public function testLabelsReturnsAllLabels()
    {
        $expected = [
            'View permissions' => 'Просмотр разрешений',
        ];

        $this->assertEquals($expected, PermissionPermission::labels());
    }
}
