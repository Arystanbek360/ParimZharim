<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\ApplicationErrors;

use Modules\Shared\Core\Application\BaseApplicationError;

class InvalidInputData extends BaseApplicationError {


    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
