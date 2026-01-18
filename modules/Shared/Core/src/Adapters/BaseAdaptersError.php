<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters;

use Exception;
use Throwable;

/**
 * Class BaseError
 */
abstract class BaseAdaptersError extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Adapters Error: ". $message, $code, $previous);
    }
}
