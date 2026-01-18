<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\ParimZharim\Objects\Domain\Models\Tag;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),  // Generates a unique word for each tag
            'is_visible_to_customers' => fake()->boolean(70)  // 70% chance that the tag is visible to customers
        ];
    }
}
