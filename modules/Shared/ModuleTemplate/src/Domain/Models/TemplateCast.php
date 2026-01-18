<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Core\Domain\BaseCast;

class TemplateCast extends BaseCast {
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement get() method.
    }

    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        // TODO: Implement set() method.
    }
}
