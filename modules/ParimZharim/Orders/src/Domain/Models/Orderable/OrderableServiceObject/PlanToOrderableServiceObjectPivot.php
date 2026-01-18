<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BasePivot;

/**
 * Class PlanToOrderableServiceObjectPivot
 *
 * @property int $plan_id
 * @property int $orderable_service_object_id
 * @property Carbon $date_from
 */
class PlanToOrderableServiceObjectPivot extends BasePivot {

        protected $table = 'orders_plan_to_object';

        protected $casts = [
            'date_from' => 'datetime'
        ];

}
