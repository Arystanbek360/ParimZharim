<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class CustomerProfileData extends BaseDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $dateOfBirth = null
    ){}
}
