<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Plan;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Schedule;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$this->seedSchedules();
        //$this->seedPlans();
    }

    /**
     * Seed Schedules.
     */
    protected function seedSchedules(): void
    {
        if (Schedule::count() == 0) {
            Schedule::factory(5)->create();  // Create 10 schedules
        }
    }

    /**
     * Seed Plans.
     */
    protected function seedPlans(): void
    {
        if (Plan::count() == 0) {
            Plan::factory(10)->create();  // Create 10 plans
        }
    }
}
