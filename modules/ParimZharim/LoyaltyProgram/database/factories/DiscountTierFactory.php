<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\LoyaltyProgram\Domain\Models\DiscountTier;
use Modules\ParimZharim\Profile\Database\Factories\CustomerFactory;

// Ensure this is the correct namespace for your Content model

/**
 * @extends Factory<DiscountTier>
 */
class DiscountTierFactory extends CustomerFactory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = DiscountTier::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       $discount_percentages = [3,5,7,10];
        return array_merge(parent::definition(), [
            'discount_percentage' => $this->faker->randomElement($discount_percentages),
            'threshold_amount' => $this->faker->randomFloat(2, 100, 1000),
            'start_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

}
