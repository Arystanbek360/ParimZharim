<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\Schedule;
use Modules\ParimZharim\Orders\Domain\Models\OrderableServiceObject\WorkingDays;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'name' => 'Общее расписание ' . fake()->unique()->numberBetween(1, 5), // Генерация уникального имени расписания с индексом.
            'min_duration' => $minDuration = fake()->numberBetween(2, 3),
            'max_duration' => $maxDuration = fake()->numberBetween($minDuration, 12),
            'confirmation_waiting_duration' => fake()->numberBetween(1, 2),
            'service_duration' => fake()->numberBetween(1, 2),
        ];
    }

}
