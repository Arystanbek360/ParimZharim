<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\ParimZharim\Profile\Domain\Models\Employee;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Permission;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedDemoUser();
        $this->seedTestData();
    }

    protected function seedDemoUser(): void
    {
        Artisan::call('devcraft-web-platform:create-all-permissions');
        try {
            $email = 'admin@parimzharim.kz';
            if (!Employee::where('email', $email)->exists()) {
                $employee = Employee::factory()->create(
                    [
                        'name' => 'Иванов Иван Иванович',
                        'phone' => '+79999999999',
                        'email' => $email,
                    ]);
                $employee->user->password = Hash::make('password');
                $employee->user->save();
                $role = Role::create(['name' => 'Демо-пользователь']);
                //attach all permission except Manage Users, View Users
                $permissions = Permission::where('name', '!=', 'Manage Users')->where('name', '!=', 'View Users')->get();
                $role->permissions()->attach($permissions);
                $employee->user->assignRole($role);
                $employee->user->assignRole(Roles::ADMIN->value);
            }
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
    /**
     * Seed test data for profiles.
     */
    protected function seedTestData(): void
    {
        Employee::factory()->count(3)->create();
        Customer::factory()->count(3)->create();
    }
}
