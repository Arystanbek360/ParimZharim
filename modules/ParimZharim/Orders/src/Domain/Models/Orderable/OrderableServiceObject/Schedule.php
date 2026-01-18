<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ParimZharim\Orders\Database\Factories\ScheduleFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Schedule
 *
 * @property int $id
 * @property string $name
 * @property array $metadata
 */
class Schedule extends BaseModel {

    use HasFactory, SoftDeletes;

    protected $table = 'orders_schedules';

    protected $casts = [
        'metadata' => 'array',
    ];


    public function orderableServiceObjects(): BelongsToMany
    {
        return $this->belongsToMany(OrderableServiceObject::class)
            ->using(ScheduleToOrderableServiceObjectPivot::class)
            ->withPivot('date_from');
    }

    /** @return ScheduleFactory */
    protected static function newFactory(): ScheduleFactory
    {
        return ScheduleFactory::new();
    }
}
