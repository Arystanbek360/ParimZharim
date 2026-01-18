<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ServiceFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * class Service
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $service_category_id
 * @property ServiceCategory $serviceCategory
 * @property float $price
 * @property boolean $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Service extends BaseModel {
    use HasFactory, SoftDeletes;

    protected $table = 'products_and_services_services';

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'service_category_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /** @return ServiceFactory */
    protected static function newFactory(): ServiceFactory
    {
        return ServiceFactory::new();
    }
}
