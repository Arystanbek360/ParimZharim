<?php declare(strict_types=1);

namespace Modules\Arista\Profile\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Arista\Profile\Database\Factories\TerminalFactory;
use Modules\Arista\Profile\Domain\Models\Customer;
use Modules\Arista\Profile\Domain\Models\Employee;
use Modules\Arista\Profile\Domain\Models\Profile;
use Modules\Arista\Profile\Domain\Models\Terminal;
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

    }
}
