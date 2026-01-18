<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Errors;

use Modules\Shared\Core\Domain\BaseError;
use Modules\Shared\Core\Domain\Errors\BaseHttpExceptionTrait;

class CannotCreateUserForThisProfile extends BaseError
{
    use BaseHttpExceptionTrait;

    public function __construct(string $clientMessage = "Невозможно создать пользователя для этого профиля")
    {
        parent::__construct($clientMessage);
    }

}
