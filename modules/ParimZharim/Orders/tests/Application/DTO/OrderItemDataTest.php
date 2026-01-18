<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Tests\Application\DTO;

use Modules\ParimZharim\Orders\Tests\TestCase;
use Modules\ParimZharim\Orders\Application\DTO\OrderItemData;

class OrderItemDataTest extends TestCase
{
    public function testConstructorAssignsValuesCorrectly()
    {
        // Arrange
        $orderableID = 10;
        $quantity = 5;
        $type = 'meal';

        // Act
        $orderItemData = new OrderItemData(
            $orderableID,
            $quantity,
            $type
        );

        // Assert
        $this->assertSame($orderableID, $orderItemData->orderableID);
        $this->assertSame($quantity, $orderItemData->quantity);
        $this->assertSame($type, $orderItemData->type);
    }
}
