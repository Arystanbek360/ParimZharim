<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class UserProfileData extends BaseDTO {
    public function __construct(
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $name = null,
        public ?string $password = null,
    ) {}
}
