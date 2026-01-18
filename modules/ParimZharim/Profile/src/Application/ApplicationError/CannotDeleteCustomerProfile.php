<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Application\ApplicationError;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Application\BaseApplicationError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class CannotDeleteCustomerProfile extends BaseApplicationError implements HttpExceptionInterface{


    private string $clientMessage;
    public function __construct(int $customerId, string $message)
    {
        $this->clientMessage = "Cannot delete customer profile with id: $customerId. Error: $message";
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
