<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class PhoneAuthenticationRequestData extends BaseDTO {
    public function __construct(
        public string $phone,
        public int $code,
        public string $device_id
    ) {}
}
