<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class EmailAuthenticationRequestData extends BaseDTO {
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
