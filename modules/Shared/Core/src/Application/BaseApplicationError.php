<?php declare(strict_types=1);

namespace Modules\Shared\Core\Application;

use Exception;
use Throwable;

/**
 * Class BaseApplicationError
 */
abstract class BaseApplicationError extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Application Error: ". $message, $code, $previous);
    }
}
