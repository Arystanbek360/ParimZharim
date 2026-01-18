<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Policies\RolePolicy;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\RolePermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RolePolicyTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        // Create necessary permissions
        Permission::create(['name' => RolePermission::VIEW_ROLES->value]);
        Permission::create(['name' => RolePermission::MANAGE_ROLES->value]);
        $this->role = Role::create(['name' => 'TestRole']);
    }

    public function testViewAnyPermission()
    {
        $user = $this->getUserWithPermission(RolePermission::VIEW_ROLES);
        $policy = new RolePolicy();

        $this->assertTrue($policy->viewAny($user));
    }

    public function testViewPermission()
    {
        $user = $this->getUserWithPermission(RolePermission::VIEW_ROLES);
          // Assuming no constraints on regular roles.
        $policy = new RolePolicy();
        $role = new Role($this->role->toArray());
        $this->assertTrue($policy->view($user, $role));
    }

    public function testCreatePermission()
    {
        $user = $this->getUserWithPermission(RolePermission::MANAGE_ROLES);
        $policy = new RolePolicy();

        $this->assertTrue($policy->create($user));
    }

    public function testUpdatePermission()
    {
        $user = $this->getUserWithPermission(RolePermission::MANAGE_ROLES);
        $role = new Role($this->role->toArray());
        $policy = new RolePolicy();

        $this->assertTrue($policy->update($user, $role));

        $superAdminRole = new Role(['name' => Roles::SUPER_ADMIN->value]);  // Assuming role name is 'SuperAdmin'.
        $adminRole = new Role(['name' => Roles::ADMIN->value]);  // Assuming role name is 'Admin'.
        $this->assertFalse($policy->update($user, $superAdminRole));
        $this->assertFalse($policy->update($user, $adminRole));
    }

    public function testDeletePermission()
    {
        $user = $this->getUserWithPermission(RolePermission::MANAGE_ROLES);
        $role = new Role($this->role->toArray());
        $policy = new RolePolicy();

        $this->assertTrue($policy->delete($user, $role));

        $superAdminRole = new Role(['name' => Roles::SUPER_ADMIN->value]);  // Assuming role name is 'SuperAdmin'.
        $adminRole = new Role(['name' => Roles::ADMIN->value]);  // Assuming role name is 'Admin'.
        $this->assertFalse($policy->delete($user, $superAdminRole));
        $this->assertFalse($policy->delete($user, $adminRole));
    }

    public function testReplicatePermission()
    {
        $user = User::factory()->create();
        $role = new Role($this->role->toArray());
        $policy = new RolePolicy();

        $this->assertFalse($policy->replicate($user, $role));
    }

    // Helper method to create a user with given permissions
    protected function getUserWithPermission(...$permissions)
    {
        $user = User::factory()->create();  // Assuming you have a User factory set up.
        foreach ($permissions as $permission) {
            $user->givePermissionTo($permission);
        }
        return $user;
    }
}
