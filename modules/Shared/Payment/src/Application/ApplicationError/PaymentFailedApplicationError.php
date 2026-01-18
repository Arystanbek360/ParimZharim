<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Application\ApplicationError;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Application\BaseApplicationError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class PaymentFailedApplicationError extends BaseApplicationError  implements HttpExceptionInterface {

    private string $clientMessage;

    public function __construct(string $message) {
        $this->clientMessage = $message;
        parent::__construct($message);
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

