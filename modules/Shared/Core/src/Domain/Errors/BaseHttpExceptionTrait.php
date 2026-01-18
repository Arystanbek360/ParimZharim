<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

trait BaseHttpExceptionTrait
{
    protected $errorMessage = 'An error occurred';
    protected $statusCode = 500;

    public function __construct($message = "", $statusCode = 500)
    {
        $this->errorMessage = $message;
        $this->statusCode = $statusCode;
        parent::__construct($message);
    }

    public function render(Request $request): Response
    {
        return response(json_encode(['message' => $this->message]), $this->getStatusCode(), $this->getHeaders());
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return ["Content-Type" => "application/json"];
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(string $message): void
    {
        $this->errorMessage = $message;
    }

}
