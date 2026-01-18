<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\Objects\Domain\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),  // Generates a unique word for each tag
        ];
    }
}
