<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;

readonly class OrderData extends BaseDTO {
    public function __construct(
        public int $serviceObjectID,
        public int $customerID,
        public int $guestsAdults,
        public int $guestsChildren,
        public string $timeFrom,
        public string $timeTo,
        public string $customerNotes,
        public ?string $paymentMethod = null,
        public ?OrderSource $source = null,
    ) {}
}
