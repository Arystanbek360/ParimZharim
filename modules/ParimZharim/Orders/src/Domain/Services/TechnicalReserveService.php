<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Modules\ParimZharim\Orders\Domain\Errors\InvalidOrderParams;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\Shared\Core\Domain\BaseDomainService;

class TechnicalReserveService extends BaseDomainService
{


    /**
     * @throws InvalidOrderParams
     */
    public static function setTechnicalReserveForObjectOnDate(OrderableServiceObject $object, Carbon $startTechnicalReserveDateTime, Carbon $endTechnicalReserveDateTime): void
    {
        Log::info('Setting technical reserve for object with id: ' . $object->id);
        Log::info('Start technical reserve date: ' . $startTechnicalReserveDateTime);
        Log::info('End technical reserve date: ' . $endTechnicalReserveDateTime);
        self::checkForCorrectTechnicalReserveTimeSlotParams($object, $startTechnicalReserveDateTime, $endTechnicalReserveDateTime);
        $object->startTechnicalReserveDateTime = $startTechnicalReserveDateTime;
        $object->endTechnicalReserveDateTime = $endTechnicalReserveDateTime;
    }

    /**
     * @throws InvalidOrderParams
     */
    private static function checkForCorrectTechnicalReserveTimeSlotParams(OrderableServiceObject $object, Carbon $startTechnicalReserveDateTime, Carbon $endTechnicalReserveDateTime): void
    {
        if ($startTechnicalReserveDateTime->gt($endTechnicalReserveDateTime)) {
            throw new \Exception('Дата начала технического резерва не может быть больше даты окончания');
        }

        OrderableObjectSlotsCalculatorService::checkIfTechnicalReserveCanBePlacedOnTimeOrFail($object, $startTechnicalReserveDateTime, $endTechnicalReserveDateTime);
    }

}
