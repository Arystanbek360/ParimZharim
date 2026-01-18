<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Modules\Shared\Documents\Database\Factories\PackageFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Database\Factories\UserFactory;
use TypeError;

class PackageQueryParamsTest extends TestCase
{
    private const int NAME = 0;
    private const int TYPES = 1;
    private const int STATUSES = 2;
    private const int IDS = 3;
    private const int CREATOR_IDS = 4;
    private const int PARENT_PACKAGE_ID = 5;

    protected array $validParams = [];
    protected array $invalidParams = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->validParams =
            [
                'name' => 'pack-name',
                'types' => ['type1', 'type2', 'тип_три', 'type4', 'type5'],
                'statuses' => ['draft', 'archive', 'АРХИВ', 'approved', 'Заверено!'],
                'ids' => [1, 2, 3, 4, 5, 6, 7, 8, 9],
                'creator_ids' => [1, 2, 1, 3, 3, 1, 3, 2, 6],
                'parent_package_id' => 45,
            ];

        $this->invalidParams =
            [
                'name' => 123,
                'types' => 12.3,
                'statuses' => [true, false],
                'ids' => [1, 2, 3.03, 4],
                'creator_ids' => [1, 2, UserFactory::new()->create()],
                'parent_package_id' => PackageFactory::new()->create(),
            ];
    }

    /**
     * Проверка: Создается объект параметров, где все параметры не назначены.
     * @return void
     */
    public function testFullEmptyCreation(): void
    {
        $this->expectException(ValidationError::class);
        $params = new PackageQueryParams();
    }

    /**
     * Проверка: Создается объект параметров, где все параметры назначены и валидны.
     * @return void
     */
    public function testFullValidCreation(): void
    {
        try {
            new PackageQueryParams
            (
                $this->validParams['name'],
                $this->validParams['types'],
                $this->validParams['statuses'],
                $this->validParams['ids'],
                $this->validParams['creator_ids'],
                $this->validParams['parent_package_id'],
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
            new PackageQueryParams
            (
                $this->invalidParams['name'],
                $this->invalidParams['types'],
                $this->invalidParams['statuses'],
                $this->invalidParams['ids'],
                $this->invalidParams['creator_ids'],
                $this->invalidParams['parent_package_id'],
            );
            $this->fail("\nMUST NOT BE  VALID ");
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
        $this->checkQueryParam(self::TYPES, $this->validParams['types'], $this->invalidParams['types']);
        $this->checkQueryParam(self::STATUSES, $this->validParams['statuses'], $this->invalidParams['statuses']);
        $this->checkQueryParam(self::IDS, $this->validParams['ids'], $this->invalidParams['ids']);
        $this->checkQueryParam(self::CREATOR_IDS, $this->validParams['creator_ids'], $this->invalidParams['creator_ids']);
        $this->checkQueryParam(self::PARENT_PACKAGE_ID, $this->validParams['parent_package_id'], $this->invalidParams['parent_package_id']);
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
            $this->fail("\nMUST BE VALID: (#$paramNumber param); " . $e->getMessage());
        }
        try {
            $this->createQueryParams($paramNumber, $invalidValue);
            $this->fail("\nMUST NOT BE  VALID ");
        } catch (ValidationError|TypeError $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Создание объекта параметров с конкретным назначенным параметром.
     * @throws ValidationError
     * @throws TypeError
     */
    protected function createQueryParams(int $paramNumber, $value): PackageQueryParams
    {
        $paramsArray = array_fill(0, 6, null);
        $paramsArray[$paramNumber] = $value;

        return new PackageQueryParams($paramsArray[0], $paramsArray[1], $paramsArray[2],
            $paramsArray[3], $paramsArray[4], $paramsArray[5]);
    }
}
