<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ParimZharim\Objects\Database\Factories\CategoryFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * class Category
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string|null $image
 * @property boolean $is_visible_to_customers
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */

class Category extends BaseModel {
    use HasFactory, SoftDeletes;

    protected $table = 'objects_categories';

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'is_visible_to_customers' => 'boolean',
    ];

    public function serviceObjects(): HasMany
    {
        return $this->hasMany(ServiceObject::class);
    }

    /** @return CategoryFactory */
    protected static function newFactory(): CategoryFactory
    {
        return CategoryFactory::new();
    }
}
