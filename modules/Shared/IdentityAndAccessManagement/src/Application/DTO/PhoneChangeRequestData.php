<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

readonly class PhoneChangeRequestData extends BaseDTO {
    public function __construct(
        public User $user,
        public string $phone,
        public int $code
    ) {}
}
