<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Tests\Application\DTO;

use Modules\ParimZharim\Orders\Tests\TestCase;
use Modules\ParimZharim\Orders\Application\DTO\OrderData;

class OrderDataTest extends TestCase
{
    public function testConstructorAssignsValuesCorrectly()
    {
        // Arrange
        $serviceObjectID = 1;
        $customerID = 2;
        $guestsAdults = 3;
        $guestsChildren = 4;
        $timeFrom = '2022-06-01 14:00:00';
        $timeTo = '2022-06-01 20:00:00';
        $customerNotes = 'Please prepare flowers.';

        // Act
        $orderData = new OrderData(
            $serviceObjectID,
            $customerID,
            $guestsAdults,
            $guestsChildren,
            $timeFrom,
            $timeTo,
            $customerNotes
        );

        // Assert
        $this->assertSame($serviceObjectID, $orderData->serviceObjectID);
        $this->assertSame($customerID, $orderData->customerID);
        $this->assertSame($guestsAdults, $orderData->guestsAdults);
        $this->assertSame($guestsChildren, $orderData->guestsChildren);
        $this->assertSame($timeFrom, $orderData->timeFrom);
        $this->assertSame($timeTo, $orderData->timeTo);
        $this->assertSame($customerNotes, $orderData->customerNotes);
    }
}
