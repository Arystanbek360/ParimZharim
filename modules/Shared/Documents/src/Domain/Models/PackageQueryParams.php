<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use TypeError;

/**
 * Класс `PackageQueryParams`
 * Этот объект-значение используется для инкапсуляции параметров запроса при поиске пакетов документов.
 * Позволяет точно и безопасно фильтровать пакеты по заданным критериям.
 *
 * @property ?string $name Название пакета (опционально).
 * @property ?array<string> $types Массив типов пакета (опционально).
 * @property ?array<string> $statuses Массив статусов пакета (опционально).
 * @property ?array<int> $ids Массив идентификаторов пакетов (опционально).
 * @property ?array<int> $creator_ids Массив идентификаторов создателей пакетов (опционально).
 * @property ?int $parent_package_id Идентификатор родительского пакета (опционально).
 */
readonly class PackageQueryParams extends BaseValueObject
{
    const int MAX_LENGTH = 100;

    /**
     * @throws ValidationError
     * @throws TypeError
     */
    public function __construct(
        public ?string $name = null,
        public ?array  $types = null,
        public ?array  $statuses = null,
        public ?array  $ids = null,
        public ?array  $creator_ids = null,
        public ?int    $parent_package_id = null
    )
    {
        $this->validateStringParams([[$name], $types, $statuses]);
        $this->validateIdParams([$ids, $creator_ids, [$parent_package_id]]);

        if ($this->QueryParamsAreEmpty())
            throw new ValidationError("Не заданы параметры запроса для поиска пакетов");
    }

    /**
     * Проверить на корректность строковые параметры.
     * @param array $stringParams Массив параметров, которые нужно провалидировать.
     * @throws ValidationError Выбрасывает исключение, если какая-либо строка не соответсвует требованиям валидации.
     */
    private function validateStringParams(array $stringParams): void
    {
        $maxLength = self::MAX_LENGTH;

        foreach ($stringParams as $stringArray) {
            if (!empty($stringArray)) {
                foreach ($stringArray as $stringValue)
                    if (!empty($stringValue)) {
                        if (strlen($stringValue) > $maxLength) {
                            throw new ValidationError("Превышен лимит длины строки (максимум: $maxLength): $stringValue");
                        }
                    }
            }
        }
    }

    /**
     * Проверить нак корректность параметры идентификаторов.
     * @param array $idParams Массив параметров идентификаторов, которые нужно провалидировать.
     * @throws ValidationError Выбрасывает исключение, если какой-либо идентификатор не соответсвует требованиям валидации.
     */
    private function validateIdParams(array $idParams): void
    {
        foreach ($idParams as $ids) {
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    if (!$id) continue;
                    if (!(is_int($id) && $id > 0))
                        throw new ValidationError("Значение не является натуральным числом: $id");
                }
            }
        }
    }

    /**
     * Проверка на наличие параметров запроса.
     * Так как массив параметров не пуст в любом случае, проводится проверка каждого параметра на истинность.
     * @return bool Вернет `false`, если есть хотя бы один параметр, который не `null\false`,
     * вернет `true`, если параметры не заданы и запрос с текущим объектом `PackageQueryParams` не имеет смысла.
     */
    private function QueryParamsAreEmpty(): bool
    {
        $params = (array)$this; // массив свойств (параметров запроса) текущего объекта PackageQueryParams
        $emptyParams = true;
        foreach ($params as $param) {
            if ($param) {
                $emptyParams = false;
                break;
            }
        }
        return $emptyParams;
    }
}
