<?php

declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Tests\Application\ApplicationError;

use Modules\ParimZharim\Orders\Tests\TestCase;
use Modules\ParimZharim\Orders\Application\ApplicationError\StatusChangeViolation;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Illuminate\Http\Request;

class StatusChangeViolationTest extends TestCase
{
    public function testExceptionMessageIsCorrect()
    {
        // Arrange
        $wishStatus = OrderStatus::COMPLETED;
        $currentStatus = OrderStatus::STARTED;

        // Act
        $exception = new StatusChangeViolation($wishStatus, $currentStatus);

        // Assert
        $expectedMessage = "Application Error: Невозможно перевести в заказ в статус 'Выполнен' для текущего статуса  'Начат'";
        $this->assertSame($expectedMessage, $exception->getMessage());
    }

    public function testRenderReturnsCorrectHttpResponse()
    {
        // Arrange
        $wishStatus = OrderStatus::COMPLETED;
        $currentStatus = OrderStatus::STARTED;
        $exception = new StatusChangeViolation($wishStatus, $currentStatus);
        $request = Request::create('/dummy-url');

        // Act
        $response = $exception->render($request);

        // Assert
        $expectedContent = '{"message":"\u041d\u0435\u0432\u043e\u0437\u043c\u043e\u0436\u043d\u043e \u043f\u0435\u0440\u0435\u0432\u0435\u0441\u0442\u0438 \u0432 \u0437\u0430\u043a\u0430\u0437 \u0432 \u0441\u0442\u0430\u0442\u0443\u0441 \'\u0412\u044b\u043f\u043e\u043b\u043d\u0435\u043d\' \u0434\u043b\u044f \u0442\u0435\u043a\u0443\u0449\u0435\u0433\u043e \u0441\u0442\u0430\u0442\u0443\u0441\u0430  \'\u041d\u0430\u0447\u0430\u0442\'"}';
        $this->assertSame(500, $response->getStatusCode());
        $this->assertSame($expectedContent, $response->getContent());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
