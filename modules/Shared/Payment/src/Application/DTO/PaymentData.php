<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;

readonly class PaymentData extends BaseDTO {
    public function __construct(
        public int $orderID,
        public int $customerID,
        public PaymentMethodType $paymentMethodType,
        public string $comment,
        public array $items
    ) {}

}
