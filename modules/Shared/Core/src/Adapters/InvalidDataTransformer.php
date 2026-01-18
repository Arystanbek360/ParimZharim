<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters;


use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Http\Request;
use Modules\Shared\Core\Domain\BaseError;


class InvalidDataTransformer extends BaseAdaptersError implements HttpExceptionInterface{

    private string $clientMessage;
    public function __construct($clientMessage = "Invalid Data Transformer")
    {
        $this->clientMessage = $clientMessage;
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
