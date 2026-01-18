<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Illuminate\Support\Carbon;
use Modules\Shared\Documents\Database\Factories\PackageFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\DocumentQueryParams;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Database\Factories\UserFactory;
use TypeError;

class DocumentQueryParamsTest extends TestCase
{
    private const int NAME = 0, SEARCH = 1, TYPES = 2, STATUSES = 3, NUMBERS = 4, IDS = 5, CREATOR_IDS = 6,
        PACKAGE_IDS = 7, TAG_IDS = 8, DATE_FROM_FROM = 9, DATE_FROM_TO = 10, DATE_TO_FROM = 11, DATE_TO_TO = 12;

    protected array $validParams = [];
    protected array $invalidParams = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->validParams =
            [
                'name' => 'doc-name',
                'search' => 'doc-name и другие параметры поиска',
                'types' => ['type1', 'type2', 'тип_три', 'type4', 'type5'],
                'statuses' => ['draft', 'archive', 'АРХИВ', 'approved', 'Заверено!'],
                'numbers' => ['123в', '5432222', 'три семь-восемь', '311рарашг1', 'dgsg123'],
                'ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
                'creator_ids' => [1, 2, 1, 3, 3, 1, 3, 2, 6],
                'package_ids' => [1, 5, 2, 33, 1, 13, 2, 6],
                'tag_ids' => [4, 5, 6, 3, 3, 2, 33, 1, 6],
                'date_from_from' => Carbon::parse('2024-08-20 15:00:00'),
                'date_from_to' => Carbon::parse('2024-09-20 15:00:00'),
                'date_to_from' => Carbon::parse('2024-10-20 15:00:00'),
                'date_to_to' => Carbon::parse('2024-11-20 15:00:00'),
            ];

        $this->invalidParams =
            [
                'name' => 123,
                'search' => ['name', 'type', 'status'],
                'types' => 12.3,
                'statuses' => [true, false],
                'numbers' => ['123в', '5432222', 123],
                'ids' => [1, 2, 3.03, 4],
                'creator_ids' => [1, 2, UserFactory::new()->create()],
                'package_ids' => [1, 5, 2, PackageFactory::new()->create()],
                'tag_ids' => [4, 5, 6, -3],
                'date_from_from' => '2024-11-20 15:00:00',
                'date_from_to' => '2024-10-20 15:00:00',
                'date_to_from' => '2024-09-20 15:00:00',
                'date_to_to' => '2024-08-20 15:00:00',
            ];
    }

    /**
     * Проверка: Ожидается ошибка при попытке создания объекта параметров, где все параметры не назначены.
     * @return void
     */
    public function testFullEmptyCreation(): void
    {
        $this->expectException(ValidationError::class);
        $params = new DocumentQueryParams();
    }

    /**
     * Проверка: Создается объект параметров, где все параметры назначены и валидны.
     * @return void
     */
    public function testFullValidCreation(): void
    {
        try {
            new DocumentQueryParams
            (
                $this->validParams['name'],
                $this->validParams['search'],
                $this->validParams['types'],
                $this->validParams['statuses'],
                $this->validParams['numbers'],
                $this->validParams['ids'],
                $this->validParams['creator_ids'],
                $this->validParams['package_ids'],
                $this->validParams['tag_ids'],
                $this->validParams['date_from_from'],
                $this->validParams['date_from_to'],
                $this->validParams['date_to_from'],
                $this->validParams['date_to_to']
            );
            $this->assertTrue(true);
        } catch (ValidationError|TypeError $e) {
            $this->fail("\nMUST BE VALID " . $e->getMessage());
        }
    }

    /**
     * Проверка: Создается объект параметров, где все параметры назначены и невалидны.
     * @return void
     */
    public function testFullInvalidCreation(): void
    {
        try {
            new DocumentQueryParams
            (
                $this->invalidParams['name'],
                $this->invalidParams['search'],
                $this->invalidParams['types'],
                $this->invalidParams['statuses'],
                $this->invalidParams['numbers'],
                $this->invalidParams['ids'],
                $this->invalidParams['creator_ids'],
                $this->invalidParams['package_ids'],
                $this->invalidParams['tag_ids'],
                $this->invalidParams['date_from_from'],
                $this->invalidParams['date_from_to'],
                $this->invalidParams['date_to_from'],
                $this->invalidParams['date_to_to']
            );
            $this->fail("\nMUST NOT BE VALID ");
        } catch (ValidationError|TypeError $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Проверка: Проверяется каждый отдельный параметр.
     * @return void
     */
    public function testDates(): void
    {
        $date_1 = Carbon::parse('2024-08-20 15:00:00');
        $date_2 = Carbon::parse('2024-09-20 15:00:00');
        $date_3 = Carbon::parse('2024-10-20 15:00:00');
        $date_4 = Carbon::parse('2024-11-20 15:00:00');

        try {
            new DocumentQueryParams(date_from_from: $date_1, date_from_to: $date_2, date_to_from: $date_3, date_to_to: $date_4);
            $this->assertTrue(true);
        } catch (ValidationError|TypeError $e) {
            $this->fail("\nMUST BE VALID SEQUENCE:
                        date_from_from: $date_1, date_from_to: $date_2, date_to_from: $date_3, date_to_to: $date_4 ); "
                . $e->getMessage());
        }

        try {
            new DocumentQueryParams(date_from_from: $date_4, date_from_to: $date_3, date_to_from: $date_2, date_to_to: $date_1);
            $this->fail("\nMUST NOT BE VALID ");
        } catch (ValidationError|TypeError $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Проверка: Проверяется каждый отдельный параметр.
     * @return void
     */
    public function testParam(): void
    {
        $this->checkQueryParam(self::NAME, $this->validParams['name'], $this->invalidParams['name']);
        $this->checkQueryParam(self::SEARCH, $this->validParams['search'], $this->invalidParams['search']);
        $this->checkQueryParam(self::TYPES, $this->validParams['types'], $this->invalidParams['types']);
        $this->checkQueryParam(self::STATUSES, $this->validParams['statuses'], $this->invalidParams['statuses']);
        $this->checkQueryParam(self::NUMBERS, $this->validParams['numbers'], $this->invalidParams['numbers']);
        $this->checkQueryParam(self::IDS, $this->validParams['ids'], $this->invalidParams['ids']);
        $this->checkQueryParam(self::CREATOR_IDS, $this->validParams['creator_ids'], $this->invalidParams['creator_ids']);
        $this->checkQueryParam(self::PACKAGE_IDS, $this->validParams['package_ids'], $this->invalidParams['package_ids']);
        $this->checkQueryParam(self::TAG_IDS, $this->validParams['tag_ids'], $this->invalidParams['tag_ids']);

        $this->checkQueryParam(self::DATE_FROM_FROM, $this->validParams['date_from_from'], $this->invalidParams['date_from_from']);
        $this->checkQueryParam(self::DATE_FROM_TO, $this->validParams['date_from_to'], $this->invalidParams['date_from_to']);
        $this->checkQueryParam(self::DATE_TO_FROM, $this->validParams['date_to_from'], $this->invalidParams['date_to_from']);
        $this->checkQueryParam(self::DATE_TO_TO, $this->validParams['date_to_to'], $this->invalidParams['date_to_to']);
    }

    /**
     * Проверка валидного и невалидного значения конкретного параметра.
     */
    protected function checkQueryParam(int $paramNumber, $validValue, $invalidValue): void
    {
        try {
            $this->createQueryParams($paramNumber, $validValue);
            $this->assertTrue(true);
        } catch (ValidationError|TypeError $e) {
            $this->fail("\nMUST BE VALID: $validValue (#$paramNumber param); " . $e->getMessage());
        }
        try {
            $this->createQueryParams($paramNumber, $invalidValue);
            $this->fail("\nMUST NOT BE VALID ");
        } catch (ValidationError|TypeError $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Создание объекта параметров с конкретным назначенным параметром.
     * @throws ValidationError
     * @throws TypeError
     */
    protected function createQueryParams(int $paramNumber, $value): DocumentQueryParams
    {
        $paramsArray = array_fill(0, 13, null);
        $paramsArray[$paramNumber] = $value;

        return new DocumentQueryParams(
            $paramsArray[0], $paramsArray[1], $paramsArray[2], $paramsArray[3], $paramsArray[4],
            $paramsArray[5], $paramsArray[6], $paramsArray[7], $paramsArray[8], $paramsArray[9],
            $paramsArray[10], $paramsArray[11], $paramsArray[12]
        );
    }
}
