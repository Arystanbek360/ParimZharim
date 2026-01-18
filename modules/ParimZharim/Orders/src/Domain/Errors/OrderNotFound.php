<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Domain\BaseError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class OrderNotFound extends BaseError implements HttpExceptionInterface{

    private string $clientMessage;
    public function __construct(int $orderID){
        $this->clientMessage = "Не найден заказ с идентификатором $orderID";
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
