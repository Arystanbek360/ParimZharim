<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Objects\Database\Factories\ServiceObjectFactory;
use Modules\Shared\Core\Domain\BaseModel;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * class ServiceObject
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int $capacity
 * @property Category $category
 * @property TagCollection $tags
 * @property boolean $is_active
 * @property array $metadata
 * @property Carbon|null $startTechnicalReserveDateTime
 * @property Carbon|null $endTechnicalReserveDateTime
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 *
 */
class ServiceObject extends BaseModel implements HasMedia{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'objects_service_objects';

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'startTechnicalReserveDateTime' => 'datetime',
        'endTechnicalReserveDateTime' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'description',
        'capacity',
        'category_id',
        'is_active',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'objects_object_to_tag', 'service_object_id', 'tag_id');
    }

    /**
     * @return ServiceObjectFactory
     */
    protected static function newFactory(): ServiceObjectFactory
    {
        return ServiceObjectFactory::new();
    }

    public function registerMediaCollections(): void {
        $this->addMediaCollection('main')->singleFile();  // Collection for a single main image
        $this->addMediaCollection('gallery');  // Collection for multiple images
    }

    /**
     * Define media conversions that can be automatically applied to uploaded images.
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(130)
            ->height(130)
            ->sharpen(10)
            ->nonQueued();  // Direct processing, consider queueing for heavy processing

        $this->addMediaConversion('detail')
            ->width(400)
            ->height(400)
            ->sharpen(10)
            ->nonQueued();  // Example for detail view
    }

    public function getObjectTimezone(): string
    {
        return 'Asia/Almaty';
    }

    public function getStartTechnicalReserveDateTimeAttribute(): ?Carbon
    {
        $startDateTime = $this->metadata['startTechnicalReserveDateTime'] ?? null;
        if ($startDateTime) {
            $startDateTime = Carbon::parse($startDateTime, $this->getObjectTimezone())->setSecond(0)->setMicrosecond(0);
        }
        return $startDateTime;
    }

    public function getEndTechnicalReserveDateTimeAttribute(): ?Carbon
    {
        $endDateTime = $this->metadata['endTechnicalReserveDateTime'] ?? null;
        if ($endDateTime) {
            $endDateTime = Carbon::parse($endDateTime, $this->getObjectTimezone())->setSecond(0)->setMicrosecond(0);
        }
        return $endDateTime;
    }

    public function setStartTechnicalReserveDateTimeAttribute(Carbon $startDateTime): void
    {
        $metadata = $this->metadata;
        $metadata['startTechnicalReserveDateTime'] = $startDateTime->format('Y-m-d H:i:s');
        $this->metadata = $metadata;
    }

    public function setEndTechnicalReserveDateTimeAttribute(Carbon $endDateTime): void
    {
        $metadata = $this->metadata;
        $metadata['endTechnicalReserveDateTime'] = $endDateTime->format('Y-m-d H:i:s');
        $this->metadata = $metadata;
    }
}
