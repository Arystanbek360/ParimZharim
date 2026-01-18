<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\RolesAndPermissions;

use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\EnumResolver;

class MockEnum {
    public static function tryFrom(string $value): ?self {
        $instances = [
            'ADMIN' => new self('Administrator'),
            'USER' => new self('User'),
        ];

        return $instances[$value] ?? null;
    }

    private string $label;

    private function __construct(string $label) {
        $this->label = $label;
    }

    public function label(): string {
        return $this->label;
    }
}

class EnumResolverTest extends TestCase
{
    public function testLabelFromValueReturnsCorrectLabel()
    {
        $enums = [MockEnum::class];
        $resolver = new EnumResolver($enums);

        $this->assertEquals('Administrator', $resolver->labelFromValue('ADMIN'));
    }

    public function testLabelFromValueReturnsInputIfNoMatch()
    {
        $enums = [MockEnum::class];
        $resolver = new EnumResolver($enums);

        $this->assertEquals('NON_EXISTENT', $resolver->labelFromValue('NON_EXISTENT'));
    }
}
