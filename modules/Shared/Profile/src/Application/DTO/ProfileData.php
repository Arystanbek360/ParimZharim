<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class ProfileData extends BaseDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $phone = null,
        public ?string $email = null,
    ){}
}
