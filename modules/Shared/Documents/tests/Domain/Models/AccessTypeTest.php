<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Tests\TestCase;

class AccessTypeTest extends TestCase
{
    public function testLabel()
    {
        $this->assertEquals('Чтение', AccessType::READ->label());
        $this->assertEquals('Комментирование', AccessType::COMMENT->label());
        $this->assertEquals('Запись', AccessType::WRITE->label());
    }

    public function testLabels()
    {
        $labels = AccessType::labels();

        $this->assertIsArray($labels);
        $this->assertEquals('Чтение', $labels[AccessType::READ->value]);
        $this->assertEquals('Комментирование', $labels[AccessType::COMMENT->value]);
        $this->assertEquals('Запись', $labels[AccessType::WRITE->value]);
    }
}
