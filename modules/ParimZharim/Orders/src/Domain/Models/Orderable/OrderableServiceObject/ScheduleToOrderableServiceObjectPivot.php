<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BasePivot;

/**
 * Class ScheduleToOrderableServiceObjectPivot
 *
 * @property int $schedule_id
 * @property int $orderable_service_object_id
 * @property Carbon $date_from
 */
class ScheduleToOrderableServiceObjectPivot extends BasePivot {

        protected $table = 'orders_schedule_to_object';

        protected $casts = [
            'date_from' => 'datetime',
        ];

}
