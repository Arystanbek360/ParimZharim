<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Класс `PackageFactory`
 * Фабрика для создания экземпляров класса `Package`.
 *
 * @extends Factory<Package>
 *
 * @example
 * $package = PackageFactory::new()->create();
 *
 * @see Package
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class PackageFactory extends Factory
{
    /**
     * Модель, связанная с фабрикой.
     */
    protected $model = Package::class;

    /**
     * Массивы возможных значений типов.
     */
    protected array $types = ['Планы', 'Проекты', 'ТЗ'];

    /**
     * Массивы возможных значений статусов.
     */
    protected array $statuses = ['Черновик', 'Заверено', 'Архив'];


    /**
     * Атрибуты объекта модели по умолчанию.
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = $this->faker->randomElement($this->types);
        $status = $this->faker->randomElement($this->statuses);
        $metadata = $this->faker->sentence;
        return [
            'name' => "Пакет \"$type\" ($status)",
            'type' => $type,
            'status' => $status,
            'creator_id' => $this->getOrCreateUserId(),
            'parent_package_id' => null,
            'metadata' => ['data' => $metadata],
            'access_mode' => AccessMode::SPECIFIC_USERS
        ];
    }

    /**
     * Выбирает случайного существующего пользователя или создает нового.
     * @return int
     */
    private function getOrCreateUserId(): int
    {
        if ($this->faker->randomElement([true, false])) {
            $user = User::query()->inRandomOrder()->first();
            if ($user instanceof User) return $user->id;
        }
        $user = User::factory()->create();

        return $user->id;
    }
}
