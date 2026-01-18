<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Errors;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Modules\Shared\Core\Domain\BaseError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class OrderableObjectNotFound extends BaseError implements HttpExceptionInterface {

    private string $clientMessage;
    public function __construct(int $orderableObjectId) {

        $this->clientMessage = "Не найден объект $orderableObjectId";
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
