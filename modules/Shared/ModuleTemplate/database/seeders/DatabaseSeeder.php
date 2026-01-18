<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Shared\ModuleTemplate\Domain\Models\Template;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Template::factory()->create([
            'name' => 'Test User'
        ]);
    }
}
