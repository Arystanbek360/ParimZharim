<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Tests\BaseTestCase;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

abstract class TestCase extends BaseTestCase
{
    public static bool $skipAllModulesTests = false;
    protected bool $skipTests = false;

    protected function setUp(): void
    {
        parent::setUp();

        if(self::$skipAllModulesTests) {
            self::markTestSkipped('Tests for the module "Shared/Documents" are disabled');
        }
        if($this->skipTests) {
            $this->markTestSkipped('Tests for the class are disabled');
        }
    }

    protected function makeAndGetTestDocument(array $attributes = [], bool $andSave = true) : Document
    {
        $creator = User::factory()->create();;
        $number = $creator->id .'Ехал грека';
        $type = 'через реку';
        $status = 'Видит грека';
        $content = 'В реке рак! Сунул грека руку в реку';
        $metadata = 'Рак за руку грека цап';

        $definition = [
            'name' => "$type $number",
            'number' => $number,
            'type' => $type,
            'status' => $status,
            'creator_id' => $creator->id,
            'package_id' => null,
            'file' => null,
            'content' => ['content-data' => $content],
            'metadata' => ['data' => $metadata],
            'date_from' => $this->getRandomDateThisYear(),
            'date_to' => null,
            'access_mode' => AccessMode::SPECIFIC_USERS,
        ];

        if(!empty($attributes)) {
            $definition = array_merge($definition, $attributes);
        }

        $doc = Document::makeDocumentInstance($definition);

        if($andSave) {
            $doc->save();
        }

        return $doc;
    }

    /**
     * Возвращает случайную дату текущего года.
     * @return Carbon
     */
    private function getRandomDateThisYear(): Carbon
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        return Carbon::createFromTimestamp(rand($startOfYear->timestamp, $endOfYear->timestamp));
    }
}
