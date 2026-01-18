<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Models;

use Modules\Shared\IdentityAndAccessManagement\Domain\RolesAndPermissions\Roles;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole {

    public function getLabelAttribute(): string
    {
        // Проверяем, установлено ли значение name
        if ($this->name === null) {
            return ''; // Возвращаем пустую строку или другое дефолтное значение
        }

        // Попытка получить enum-объект из значения name
        $roleEnum = Roles::tryFrom($this->name);

        // Если enum существует, возвращаем его label, если нет - возвращаем name
        return $roleEnum ? $roleEnum->label() : $this->name;
    }

}
