<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\ParimZharim\Objects\Database\Factories\TagFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Tag
 * @package Modules\ParimZharim\Objects\Domain\Models
 *
 * @property int $id
 * @property string $name
 * @property string $image
 * @property boolean $is_visible_to_customers
 *
 */
class Tag extends BaseModel {
    use HasFactory, SoftDeletes;

    protected $table = 'objects_tags';

    protected $fillable = [
        'name'
    ];

    protected $casts = [
        'is_visible_to_customers' => 'boolean'
    ];

    public function serviceObjects(): BelongsToMany
    {
        return $this->belongsToMany(ServiceObject::class, 'objects_object_to_tag', 'tag_id', 'service_object_id');
    }

    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }
}
