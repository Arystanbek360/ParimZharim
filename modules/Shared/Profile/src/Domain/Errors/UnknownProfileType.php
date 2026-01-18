<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Errors;

use Modules\Shared\Core\Domain\BaseError;
use Modules\Shared\Core\Domain\Errors\BaseHttpExceptionTrait;

/**
 * Ошибка UnknownProfileType
 *
 * Представляет ошибку, возникающую при попытке создания профиля с неизвестным типом.
 *
 * @example
 * throw new UnknownProfileType("Неизвестный тип профиля");
 *
 * @see OrganizationUnit
 *
 * @version 1.0.0
 * @since 2024-06-25
 */
class UnknownProfileType extends BaseError {

    use BaseHttpExceptionTrait;

    public function __construct(string $clientMessage = "Неизвестный тип профиля")
    {
        parent::__construct($clientMessage);
    }

}
