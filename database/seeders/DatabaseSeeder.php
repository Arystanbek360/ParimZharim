<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            \Modules\Shared\IdentityAndAccessManagement\Database\Seeders\DatabaseSeeder::class,
            \Modules\Shared\CMS\Database\Seeders\DatabaseSeeder::class,
            \Modules\ParimZharim\Objects\Database\Seeders\DatabaseSeeder::class,
            \Modules\ParimZharim\Profile\Database\Seeders\DatabaseSeeder::class,
            \Modules\ParimZharim\ProductsAndServices\Database\Seeders\DatabaseSeeder::class,
            \Modules\ParimZharim\Orders\Database\Seeders\DatabaseSeeder::class,
            \Modules\ParimZharim\LoyaltyProgram\Database\Seeders\DatabaseSeeder::class,
            \Modules\Shared\Payment\Database\Seeders\DatabaseSeeder::class,
            \Modules\Shared\Security\Database\Seeders\DatabaseSeeder::class
        ]);
    }
}
