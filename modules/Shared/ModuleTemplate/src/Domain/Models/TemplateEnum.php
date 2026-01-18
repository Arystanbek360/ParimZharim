<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Domain\Models;

use Modules\Shared\Core\Domain\BaseEnum;
use Modules\Shared\Core\Domain\Enums\BaseEnumTrait;

enum TemplateEnum: string implements BaseEnum
{
    use BaseEnumTrait;

    case TEMPLATE = 'TEMPLATE';

    public function label(): string
    {
        return match ($this) {
            self::TEMPLATE => 'Template',
        };
    }
}
