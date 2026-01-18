<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Plan;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\PlanType;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    protected $model = Plan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $timeRanges = [
            ['00:00', '17:59'],
            ['18:00', '23:59']
        ];

        $timeRange = fake()->randomElement($timeRanges);

        return [
            'name' => fake()->randomElement([
                'Классическая русская баня',
                'Люкс сауна с бассейном',
                'Открытая беседка',
                'Закрытая беседка для мероприятий'
            ]), // Clear and meaningful names for saunas, baths, or gazebos.
            'plan_type' => fake()->randomElement(PlanType::cases())->value, // Randomly picks a plan type.
            'time_from' => $timeRange[0], // Fixed start time.
            'time_to' => $timeRange[1], // Fixed end time.
            'price' => fake()->numberBetween(2, 10) * 1000 // Generates a price multiple of 1000, between 2000 and 10000.
        ];
    }
}
