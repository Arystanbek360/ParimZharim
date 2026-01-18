<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shared\Documents\Domain\Models\Tag;

/**
 * Класс `TagFactory`
 * Фабрика для создания экземпляров класса `Tag`.
 *
 * @extends Factory<Tag>
 *
 * @example
 * $tag = TagFactory::new()->create();
 *
 * @see Tag
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class TagFactory extends Factory
{
    /**
     * Модель, связанная с фабрикой.
     */
    protected $model = Tag::class;

    /**
     * Атрибуты объекта модели по умолчанию.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word,
        ];
    }
}
