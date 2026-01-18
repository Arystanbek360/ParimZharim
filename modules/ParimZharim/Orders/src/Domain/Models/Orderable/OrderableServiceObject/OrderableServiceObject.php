<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\ParimZharim\Objects\Domain\Models\ServiceObject;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableService;

/**
 * Class OrderableServiceObject full extends ServiceObject
 */
class OrderableServiceObject extends ServiceObject {

    protected $table = 'objects_service_objects';

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'orders_schedule_to_object', 'orderable_service_object_id', 'schedule_id')
            ->using(ScheduleToOrderableServiceObjectPivot::class)
            ->withPivot('date_from');
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class, 'orders_plan_to_object', 'orderable_service_object_id', 'plan_id')
            ->using(PlanToOrderableServiceObjectPivot::class)
            ->withPivot('date_from');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(OrderableService::class, 'orders_service_to_object', 'object_id', 'service_id')
            ->withTimestamps();
    }
}
