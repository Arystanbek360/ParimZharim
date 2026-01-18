<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Infrastructure\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Infrastructure\BaseInfrastructureError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Custom error to be thrown when a validation type is incorrect.
 */
class CantRecreateModelError extends BaseInfrastructureError implements HttpExceptionInterface
{
    private string $clientMessage;

    public function __construct(string $message = "Не удалось воссоздать модель из базы данных")
    {
        $this->clientMessage = $message;
        parent::__construct($message);
    }

    public function render(Request $request): Response
    {
        return response(json_encode(['message' => $this->clientMessage]), $this->getStatusCode(), $this->getHeaders());
    }

    public function getStatusCode(): int
    {
        return 422;
    }

    public function getHeaders(): array
    {
        return ["Content-Type" => "application/json"];
    }
}
