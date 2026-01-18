<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Models;

use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\EnumResolver;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission {

    public function getLabelAttribute(): string
    {
        $enumResolver = new EnumResolver(app()->get(EnumResolver::ENUM_RESOLVER_CONTAINER));
        return $enumResolver->labelFromValue($this->attributes['name']);
    }

    public function getDescriptionAttribute(): string
    {
        $enumResolver = new EnumResolver(app()->get(EnumResolver::ENUM_RESOLVER_CONTAINER));
        return $enumResolver->descriptionFromValue($this->attributes['name']);
    }
}
