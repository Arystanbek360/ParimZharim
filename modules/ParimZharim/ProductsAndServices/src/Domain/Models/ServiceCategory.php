<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\ProductsAndServices\Database\Factories\ServiceCategoryFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * class ServiceCategory
 *
 * @property int $id
 * @property string $name
 * @property boolean $is_visible_to_customers
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class ServiceCategory extends BaseModel {
    use HasFactory, SoftDeletes;

    protected $table = 'products_and_services_service_categories';

    protected $fillable = [
        'name',
    ];

    protected $casts = [
        'is_visible_to_customers' => 'boolean',
    ];

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    /** @return ServiceCategoryFactory */
    protected static function newFactory(): ServiceCategoryFactory
    {
        return ServiceCategoryFactory::new();
    }
}
