<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Models;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseValueObject;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use TypeError;

/**
 * Класс `DocumentQueryParams`
 * Этот объект-значение применяется для инкапсуляции параметров запроса при поиске документов.
 * Обеспечивает строгую валидацию входящих данных, чтобы гарантировать корректность запросов к базе данных.
 *
 * @property ?string $name Название документа (опционально).
 * @property ?string $search Строка для поиска по содержимому документа (опционально)
 * @property ?array $types Массив типов документов (опционально).
 * @property ?array $statuses Массив статусов документов (опционально).
 * @property ?array $numbers Массив номеров документов (опционально).
 * @property ?array $ids Массив идентификаторов документов (опционально).
 * @property ?array $creator_ids Массив идентификаторов создателей (опционально).
 * @property ?array $package_ids Массив идентификаторов пакетов (опционально).
 * @property ?array $tag_ids Массив идентификаторов тегов (опционально).
 * @property Carbon|null $date_from_from Начальная дата действия документа (опционально).
 * @property Carbon|null $date_from_to Конечная дата начала действия документа (опционально).
 * @property Carbon|null $date_to_from Начальная дата окончания действия документа (опционально).
 * @property Carbon|null $date_to_to Конечная дата окончания действия документа (опционально).
 * @property bool $only_last_version Флаг для изменения запроса для выборки только последних версий (опционально).
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
readonly class DocumentQueryParams extends BaseValueObject
{
    private const int MAX_LENGTH = 100;

    /**
     * @throws ValidationError
     * @throws TypeError
     */
    public function __construct(
        public ?string $name = null,
        public ?string $search = null,
        public ?array  $types = null,
        public ?array  $statuses = null,
        public ?array  $numbers = null,
        public ?array  $ids = null,
        public ?array  $creator_ids = null,
        public ?array  $package_ids = null,
        public ?array  $tag_ids = null,
        public ?Carbon $date_from_from = null,
        public ?Carbon $date_from_to = null,
        public ?Carbon $date_to_from = null,
        public ?Carbon $date_to_to = null,
        public bool    $only_last_version = false
    )
    {
        $this->validateStringParams([[$name], [$search], $types, $statuses, $numbers]);
        $this->validateIdParams([$ids, $creator_ids, $package_ids, $tag_ids]);
        $this->validateDates($date_from_from, $date_from_to, $date_to_from, $date_to_to);

        if ($this->QueryParamsAreEmpty())
            throw new ValidationError("Не заданы параметры запроса для поиска документов");
    }

    /**
     * Проверить на корректность строковые параметры: длина строки не превышает максимальное значение.
     * @param array $stringParams Массив строковых параметров, которые нужно провалидировать.
     * @throws ValidationError Выбрасывает исключение, если какая-либо строка не соответсвует требованиям валидации.
     */
    private function validateStringParams(array $stringParams): void
    {
        $maxLength = self::MAX_LENGTH;

        foreach ($stringParams as $stringArray) {
            if (!empty($stringArray)) {
                foreach ($stringArray as $stringValue) {
                    if (!empty($stringValue)) {
                        if (strlen($stringValue) > $maxLength) {
                            throw new ValidationError("Превышен лимит длины строки (максимум: $maxLength): $stringValue");
                        }
                    }
                }
            }
        }
    }

    /**
     * Проверить на корректность параметры идентификаторов: все идентификаторы должны быть натуральными числами.
     * @param array $idParams Массив параметров идентификаторов, которые нужно провалидировать.
     * @throws ValidationError Выбрасывает исключение, если какой-либо идентификатор не соответсвует требованиям валидации.
     */
    private function validateIdParams(array $idParams): void
    {
        foreach ($idParams as $ids) {
            if (!empty($ids)) {
                foreach ($ids as $id) {
                    if (!(is_int($id) && $id > 0))
                        throw new ValidationError("Значение не является натуральным числом: $id");
                }
            }
        }
    }

    /**
     * Валидирует значения дат на корректность хронологии. Даты в параметрах должны идти по возрастанию.
     * @param ?Carbon $from_from Дата начала действия документа (нижняя граница значения).
     * @param ?Carbon $from_to Дата начала действия документа (верхняя граница значения).
     * @param ?Carbon $to_from Дата окончания действия документа (нижняя граница значения).
     * @param ?Carbon $to_to Дата окончания действия документа (верхняя граница значения).
     * @throws ValidationError Выбрасывает исключение, если какая-либо из дат нарушает хронологический порядок.
     */
    private function validateDates(?Carbon $from_from, ?Carbon $from_to, ?Carbon $to_from, ?Carbon $to_to): void
    {
        $dateSequence = [];
        if ($from_from)
            $dateSequence[] = $from_from;
        if ($from_to)
            $dateSequence[] = $from_to;
        if ($to_from)
            $dateSequence[] = $to_from;
        if ($to_to)
            $dateSequence[] = $to_to;

        $count = count($dateSequence) - 1;

        for ($i = 0; $i < $count; $i++) {
            if ($dateSequence[$i]->greaterThanOrEqualTo($dateSequence[$i + 1])) {
                $date1 = $dateSequence[$i]->format('d.m.Y');
                $date2 = $dateSequence[$i + 1]->format('d.m.Y');
                throw new ValidationError("Некорректные даты: $date1 не может быть позже $date2");
            }
        }
    }

    /**
     * Проверка на наличие параметров запроса.
     * Так как массив параметров не пуст в любом случае, проводится проверка каждого параметра на истинность.
     * @return bool Вернет `false`, если есть хотя бы один параметр, который не `null\false`,
     * вернет `true`, если параметры не заданы и запрос с текущим объектом `DocumentQueryParams` не имеет смысла.
     */
    private function QueryParamsAreEmpty(): bool
    {
        $params = (array)$this; // массив свойств (параметров запроса) текущего объекта DocumentQueryParams
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
