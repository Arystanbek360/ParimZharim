<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ParimZharim\Orders\Database\Factories\PlanFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Plan
 *
 * @property int $id
 * @property string $name
 * @property PlanType $plan_type
 * @property array $metadata
 */
class Plan extends BaseModel {

    use HasFactory, SoftDeletes;

    protected $table = 'orders_plans';

    protected $casts = [
        'metadata' => 'array',
        'plan_type' => PlanType::class,
    ];

    public function orderableServiceObjects(): BelongsToMany
    {
        return $this->belongsToMany(OrderableServiceObject::class)
            ->using(PlanToOrderableServiceObjectPivot::class)
            ->withPivot('date_from');
    }

    /** @return PlanFactory */
    protected static function newFactory(): PlanFactory
    {
        return PlanFactory::new();
    }

    /**
     * Отсортировать дни при сохранении
     *
     * @param $value
     * @return void
     */
    /*public function setConcreteDaysAttribute($value): void
    {
        $sortedDates = collect($value)->sort(function ($a, $b) {
            return strtotime($a) <=> strtotime($b);
        });

        $this->attributes['concrete_days'] = json_encode(array_values($sortedDates->toArray()));
    }*/
}
