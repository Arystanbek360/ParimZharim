<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Tests\Domain\Policies;

use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Policies\CategoryPolicy;
use Modules\ParimZharim\Objects\Domain\RolesAndPermissions\ObjectPermission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Spatie\Permission\Models\Permission;
use Modules\ParimZharim\Objects\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new CategoryPolicy();

        // Создание разрешений для тестирования
        Permission::create(['name' => ObjectPermission::VIEW_OBJECTS, 'guard_name' => 'web']);
        Permission::create(['name' => ObjectPermission::MANAGE_OBJECTS, 'guard_name' => 'web']);
    }

    public function test_view_any_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::VIEW_OBJECTS);

        $userWithoutPermission = User::factory()->create();

        $this->assertTrue($this->policy->viewAny($userWithPermission));
        $this->assertNull($this->policy->viewAny($userWithoutPermission));
    }

    public function test_view_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::VIEW_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue($this->policy->view($userWithPermission, $category));
        $this->assertNull($this->policy->view($userWithoutPermission, $category));
    }

    public function test_create_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();

        $this->assertTrue($this->policy->create($userWithPermission));
        $this->assertNull($this->policy->create($userWithoutPermission));
    }


    public function test_update_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue($this->policy->update($userWithPermission, $category));
        $this->assertNull($this->policy->update($userWithoutPermission, $category));
    }

    public function test_replicate_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue($this->policy->replicate($userWithPermission, $category));
        $this->assertNull($this->policy->replicate($userWithoutPermission, $category));
    }

    public function test_force_delete_permission()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertFalse($this->policy->forceDelete($user, $category));
    }

    public function test_delete_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue($this->policy->delete($userWithPermission, $category));
        $this->assertNull($this->policy->delete($userWithoutPermission, $category));
    }

    public function test_restore_permission()
    {
        $userWithPermission = User::factory()->create();
        $userWithPermission->givePermissionTo(ObjectPermission::MANAGE_OBJECTS);

        $userWithoutPermission = User::factory()->create();
        $category = Category::factory()->create();

        $this->assertTrue($this->policy->restore($userWithPermission, $category));
        $this->assertNull($this->policy->restore($userWithoutPermission, $category));
    }
}
