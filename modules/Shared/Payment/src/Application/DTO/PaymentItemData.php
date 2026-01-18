<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;

readonly class PaymentItemData extends BaseDTO {
    public function __construct(
        public string $name,
        public float $price,
        public int $quantity
    ) {}
}
