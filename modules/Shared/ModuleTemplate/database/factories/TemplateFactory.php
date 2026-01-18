<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shared\ModuleTemplate\Domain\Models\Template;

/**
 * @extends Factory<Template>
 */
class TemplateFactory extends Factory
{
    /**
     * The name of the model that this factory creates.
     */
    protected $model = Template::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name()
        ];
    }
}
