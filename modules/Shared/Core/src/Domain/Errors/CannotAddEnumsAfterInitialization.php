<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain\Errors;

use Modules\Shared\Core\Domain\BaseError;

class CannotAddEnumsAfterInitialization extends BaseError
{
    use BaseHttpExceptionTrait;

    public function __construct(string $errorMessage = "Cannot add enums after initialization")
    {
        parent::__construct($errorMessage);
    }
}
