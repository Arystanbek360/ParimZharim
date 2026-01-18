<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\ApplicationError;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Shared\Core\Application\BaseApplicationError;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class WrongActualAdvancedPayment extends BaseApplicationError implements HttpExceptionInterface {


    private string $clientMessage;
    /**
     * Constructs the exception for order status change errors.
     *
     * @param int $expectedAdvancePayment
     */
    public function __construct(int $expectedAdvancePayment)
    {
        $this->clientMessage = sprintf(
            'Фактическая предоплата не может быть меньше ожидаемой %d KZT',
            $expectedAdvancePayment
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
