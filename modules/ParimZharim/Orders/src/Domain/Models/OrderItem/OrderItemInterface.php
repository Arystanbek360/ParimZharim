<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\OrderItem;


interface OrderItemInterface
{
    public function calculatePrice(): float;

    public function calculateTotal(): float;

    public function calculateDiscount(): float;

    public function calculateTotalWithDiscount(): float;
}
