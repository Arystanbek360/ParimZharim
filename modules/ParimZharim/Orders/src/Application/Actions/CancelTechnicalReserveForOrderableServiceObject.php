<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\Orders\Domain\Repositories\OrderableServiceObjectRepository;
use Modules\ParimZharim\Orders\Domain\Services\TechnicalReserveService;
use Modules\Shared\Core\Application\BaseAction;

class CancelTechnicalReserveForOrderableServiceObject extends BaseAction {

    public function __construct(
        private readonly OrderableServiceObjectRepository $orderableServiceObjectRepository,
    )
    {}

    /**
     * @throws OrderableObjectNotFound
     */
    public function handle(int $orderableServiceObjectID): void
    {
        $orderableServiceObject = GetOrderableServiceObjectByID::make()->handle($orderableServiceObjectID);
        if (!$orderableServiceObject) {
            throw new OrderableObjectNotFound($orderableServiceObjectID);
        }
        TechnicalReserveService::cancelTechnicalReserve($orderableServiceObject);
        $this->orderableServiceObjectRepository->save($orderableServiceObject);
    }

}
