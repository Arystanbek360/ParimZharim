<?php //declare(strict_types=1);
//
//namespace Modules\Shared\Documents\Database\Factories;
//
//use Illuminate\Database\Eloquent\Factories\Factory;
//use Illuminate\Database\Eloquent\Model;
//use Illuminate\Support\Carbon;
//use Modules\Shared\Documents\Domain\Models\AccessMode;
//use Modules\Shared\Documents\Domain\Models\Document;
//use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
//
///**
// * Класс `DocumentFactory`
// * Фабрика для создания экземпляров анонимного класса, наследующего класс `Document`.
// *
// * @extends Factory<Document>
// *
// * @example
// * $document = DocumentFactory::new()->create();
// *
// * @see Document
// *
// * @version 1.0.0
// * @since 2024-08-21
// */
//class DocumentFactory extends Factory
//{
//    /**
//     * Модель, связанная с фабрикой.
//     */
//    protected $model = Document::class;
//
//    /**
//     * Массивы возможных значений типов.
//     */
//    protected array $types = ['План', 'Проект', 'Техническое Задание'];
//
//    /**
//     * Массивы возможных значений статусов.
//     */
//    protected array $statuses = ['Черновик', 'Заверено', 'Архив'];
//
//    /**
//     * Атрибуты объекта модели по-умолчанию.
//     * @return array<string, mixed>
//     */
//    public function definition(): array
//    {
//        $number = fake()->unique()->uuid();
//        $type = $this->faker->randomElement($this->types);
//        $status = $this->faker->randomElement($this->statuses);
//        $content = $this->faker->sentence;
//        $metadata = $this->faker->sentence;
//        return [
//            'name' => "$type $number",
//            'number' => $number,
//            'type' => $type,
//            'status' => $status,
//            'creator_id' => $this->getOrCreateUserId(),
//            'package_id' => null,
//            'file' => null,
//            'content' => ['content-data' => $content],
//            'metadata' => ['data' => $metadata],
//            'date_from' => $this->getRandomDateThisYear(),
//            'date_to' => null,
//            'access_mode' => AccessMode::SPECIFIC_USERS,
//        ];
//    }
//
//    /**
//     * Создает экземпляр анонимного класса, наследующего абстрактный класс.
//     * @param array $attributes
//     * @param Model|null $parent
//     * @return Document
//     */
//    public function make($attributes = [], ?Model $parent = null): Document
//    {
//        $attributes = array_merge($this->definition(), $attributes);
//        return Document::makeDocumentInstance($attributes);
//    }
//
//    /**
//     * Создает и сохраняет в БД экземпляр анонимного класса, наследующего абстрактный класс.
//     * @param array $attributes
//     * @param Model|null $parent
//     * @return Document
//     */
//    public function create($attributes = [], ?Model $parent = null): Document
//    {
//        $document = $this->make($attributes, $parent);
//        $document->save();
//        return $document;
//    }
//
//    /**
//     * Выбирает случайного существующего пользователя или создает нового.
//     * @return int
//     */
//    private function getOrCreateUserId(): int
//    {
//        if ($this->faker->randomElement([true, false])) {
//            $user = User::query()->inRandomOrder()->first();
//            if ($user instanceof User) return $user->id;
//        }
//
//        $user = User::factory()->create();
//
//        return $user->id;
//    }
//
//    /**
//     * Возвращает случайную дату текущего года.
//     * @return Carbon
//     */
//    private function getRandomDateThisYear(): Carbon
//    {
//        $startOfYear = Carbon::now()->startOfYear();
//        $endOfYear = Carbon::now()->endOfYear();
//
//        return Carbon::createFromTimestamp(rand($startOfYear->timestamp, $endOfYear->timestamp));
//    }
//}
