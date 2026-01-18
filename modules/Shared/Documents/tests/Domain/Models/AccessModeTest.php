<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Tests\TestCase;


class AccessModeTest extends TestCase
{
    public function testLabel()
    {
        $this->assertEquals('Все пользователи', AccessMode::ANY_USER->label());
        $this->assertEquals('Конкретные пользователи', AccessMode::SPECIFIC_USERS->label());
    }

    public function testLabels()
    {
        $labels = AccessMode::labels();

        $this->assertIsArray($labels);
        $this->assertEquals('Все пользователи', $labels[AccessMode::ANY_USER->value]);
        $this->assertEquals('Конкретные пользователи', $labels[AccessMode::SPECIFIC_USERS->value]);
    }
}
