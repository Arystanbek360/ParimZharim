<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Services\OrderableObjectSlotsCalculatorService;
use Modules\Shared\Core\Application\BaseAction;

class GetSlotsAndDurationForServiceObjectOnDate extends BaseAction {

    public function handle(int $orderableServiceObjectID, Carbon $startDate, Carbon $endDate): array
    {
        return OrderableObjectSlotsCalculatorService::getSlotsAndDurationForServiceObjectOnDate($orderableServiceObjectID, $startDate, $endDate);
    }

}
