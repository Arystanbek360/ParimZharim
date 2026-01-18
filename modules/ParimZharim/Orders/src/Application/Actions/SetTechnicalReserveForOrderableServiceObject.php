<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;


use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Errors\InvalidOrderParams;
use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\ParimZharim\Orders\Domain\Services\TechnicalReserveService;
use Modules\Shared\Core\Application\BaseAction;

class SetTechnicalReserveForOrderableServiceObject extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    /**
     * @throws InvalidOrderParams
     * @throws OrderableObjectNotFound
     */
    public function handle(int $orderableServiceObjectID, Carbon $startTechnicalReserveDateTime, Carbon $endTechnicalReserveDateTime): void
    {
        $orderableServiceObject = GetOrderableServiceObjectByID::make()->handle($orderableServiceObjectID);
        if (!$orderableServiceObject) {
            throw new OrderableObjectNotFound($orderableServiceObjectID);
        }
        TechnicalReserveService::setTechnicalReserveForObjectOnDate($orderableServiceObject, $startTechnicalReserveDateTime, $endTechnicalReserveDateTime);
        $this->orderableServiceObjectRepository->save($orderableServiceObject);
    }

}
