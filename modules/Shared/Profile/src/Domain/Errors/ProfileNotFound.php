<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Errors;

use Modules\Shared\Core\Domain\BaseError;
use Modules\Shared\Core\Domain\Errors\BaseHttpExceptionTrait;

class ProfileNotFound extends BaseError {

    use BaseHttpExceptionTrait;
    public function __construct(int $customerId)
    {
        $message = "Профиль с идентификатором $customerId не найден";
        parent::__construct($message);
    }
}
