<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain;

/**
 * Class BaseEnum
 */
interface BaseEnum
{
    public function label(): string;
    public static function labels(): array;
}
