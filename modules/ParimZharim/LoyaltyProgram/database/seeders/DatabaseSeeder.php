<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTier;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        DiscountTier::factory()->count(4)->create()->each(function ($discountTier) {
            DiscountTier::firstOrCreate(['discount_percentage' => $discountTier->discount_percentage], $discountTier->toArray());
        });
    }

}
