<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\DTO;

use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;

readonly class PaymentItemCollectionData extends BaseDTO {

    public array $items;

    /**
     * PaymentItemsCollection constructor.
     * @param PaymentItemData[] $items
     */
    public function __construct(array $items) {
        $this->items = $items;
    }
}
