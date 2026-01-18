<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shared\CMS\Domain\Models\Content;

/**
 * @extends Factory<Content>
 */
class ContentFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Content::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6, true),  // Generates a random sentence with exactly 6 words.
            'slug' => $this->faker->slug(),              // Generates a random slug.
            'content' => $this->faker->paragraphs(3, true), // Generates a string consisting of 3 paragraphs.
            'created_at' => now(),                        // Uses the current date and time for creation.
            'updated_at' => now()                         // Uses the current date and time for last update.
        ];
    }
}
