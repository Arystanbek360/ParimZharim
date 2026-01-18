<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BaseEvent
 */
abstract class BaseEvent
{
    use Dispatchable, SerializesModels;
}
