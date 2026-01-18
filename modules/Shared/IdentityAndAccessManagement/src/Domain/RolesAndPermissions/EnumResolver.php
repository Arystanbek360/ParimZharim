<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions;

class EnumResolver
{
    const string ENUM_RESOLVER_CONTAINER = 'ENUM_RESOLVER_CONTAINER';
    protected array $enums;

    public function __construct(array $enums)
    {
        $this->enums = $enums;
    }

    public function labelFromValue(string $value): ?string
    {
        foreach ($this->enums as $enumClass) {
            $label = $enumClass::tryFrom($value)?->label();
            if ($label !== null) {
                return $label;
            }
        }

        return $value;
    }

    public function descriptionFromValue(string $value): ?string
    {
        foreach ($this->enums as $enumClass) {
            $description = $enumClass::tryFrom($value)?->description();
            if ($description !== null) {
                return $description;
            }
        }

        return $value;
    }
}
