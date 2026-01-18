<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\ApplicationError;

use Modules\Shared\Core\Application\BaseApplicationError;

class InvalidInputData extends BaseApplicationError {


    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
