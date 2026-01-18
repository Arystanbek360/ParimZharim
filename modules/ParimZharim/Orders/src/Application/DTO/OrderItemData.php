<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;

readonly class OrderItemData extends BaseDTO {
    public function __construct(
        public int $orderableID,
        public int $quantity,
        public string $type
    ) {}
}
