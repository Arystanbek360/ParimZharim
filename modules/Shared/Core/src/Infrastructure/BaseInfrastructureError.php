<?php declare(strict_types=1);

namespace Modules\Shared\Core\Infrastructure;

use Exception;
use Throwable;

/**
 * Class BaseError
 */
abstract class BaseInfrastructureError extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Infrastructure Error: ". $message, $code, $previous);
    }
}
