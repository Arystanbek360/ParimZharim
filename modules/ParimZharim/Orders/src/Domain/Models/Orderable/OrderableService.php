<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models\Orderable;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\ParimZharim\Orders\Domain\Models\Orderable\OrderableServiceObject\OrderableServiceObject;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;

/**
 * Class OrderableService full extends Service
 */
class OrderableService extends Service {
    public function orderableServiceObjects(): BelongsToMany
    {
        return $this->belongsToMany(OrderableServiceObject::class, 'orders_service_to_object', 'service_id', 'object_id')
            ->withTimestamps();
    }
}
