<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\ApplicationError;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Application\BaseApplicationError;
use Modules\ParimZharim\Orders\Domain\Models\OrderStatus;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class StatusChangeViolation extends BaseApplicationError implements HttpExceptionInterface {


    private string $clientMessage;
    /**
     * Constructs the exception for order status change errors.
     *
     * @param OrderStatus $wishStatus
     * @param OrderStatus $currentStatus Current status of the order
     */
    public function __construct(OrderStatus $wishStatus, OrderStatus $currentStatus)
    {
        $this->clientMessage = sprintf(
            "Невозможно перевести в заказ в статус '%s' для текущего статуса  '%s'",
            $wishStatus->label(),
            $currentStatus->label()
        );
        parent::__construct($this->clientMessage);
    }

    public function render(Request $request): Response
    {
        return response(json_encode(['message' => $this->clientMessage]), $this->getStatusCode(), $this->getHeaders());
    }

    public function getStatusCode(): int
    {
        return 500;
    }

    public function getHeaders(): array
    {
        return ["Content-Type" => "application/json"];
    }
}
