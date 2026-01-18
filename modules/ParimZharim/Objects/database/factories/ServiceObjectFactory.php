<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;

/**
 * @extends Factory<ServiceObject>
 */
class ServiceObjectFactory extends Factory
{
    protected $model = ServiceObject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),  // Generates a string of 3 words
            'description' => fake()->text(200),  // Generates a random text of up to 200 characters
            'capacity' => fake()->numberBetween(1, 100),  // Generates a random number between 1 and 100
            'category_id' => Category::factory(),  // Automatically creates a Category and uses its ID
        ];
    }
}
