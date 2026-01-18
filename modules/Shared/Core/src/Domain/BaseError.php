<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain;

use Exception;
use Modules\Shared\Core\Domain\Errors\BaseHttpExceptionTrait;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Class BaseError
 */
abstract class BaseError extends Exception  implements HttpExceptionInterface
{
    use BaseHttpExceptionTrait;
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct("Domain Error: ". $message, $code, $previous);
    }
}
