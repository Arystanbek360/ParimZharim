<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Application\Actions;

use Illuminate\Support\Carbon;
use Modules\ParimZharim\Orders\Domain\Services\OrderableObjectSlotsCalculatorService;
use Modules\Shared\Core\Application\BaseAction;

class GetMergedFreeSlotsForServiceObjectOnDate extends BaseAction {

    public function handle(int $orderableServiceObjectID, Carbon $startDate, Carbon $endDate, int $minutesStep = 30): array
    {
        return OrderableObjectSlotsCalculatorService::getMergedFreeSlotsForObjectOnDate($orderableServiceObjectID, $startDate, $endDate, $minutesStep);
    }
}
