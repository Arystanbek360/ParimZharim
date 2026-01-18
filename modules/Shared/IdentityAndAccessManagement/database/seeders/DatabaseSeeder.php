<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedRoles();
        $this->createSuperAdmin();
    }

    /**
     * Сидирование ролей.
     */
    protected function seedRoles(): void
    {
        foreach (Roles::cases() as $role) {
            try {
                Role::firstOrCreate(['name' => $role->value]);
            } catch (Throwable $e) {
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }

    /**
     * Создание суперадминистратора.
     */
    protected function createSuperAdmin(): void
    {
        try {
            $user = User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'super@admin.com',
            ]);
            $user->assignRole(Roles::SUPER_ADMIN->value);
        } catch (Throwable $e) {
            echo $e->getMessage() . PHP_EOL;
        }
    }
}
