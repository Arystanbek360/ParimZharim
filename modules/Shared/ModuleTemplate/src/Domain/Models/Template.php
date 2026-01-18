<?php declare(strict_types=1);

namespace Modules\Shared\ModuleTemplate\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\ModuleTemplate\Database\Factories\TemplateFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


/**
 * Class Template
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $display_text
 * @property string $email
 * @property string $country
 * @property string $photo
 * @property string $slug
 * @property int $number
 * @property TemplateEnum $type
 * @property Carbon $date
 * @property ?string $url
 * @property float $price
 * @property bool $active
 * @property array $options
 * @property array $metadata
 * @property array $template_data
 */
class Template extends BaseModel implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $table = 'template_templates';

    protected $fillable = [
        'name',
        'description',
        'number',
        'type',
        'date',
        'url',
        'price',
        'active',
        'options',
        'metadata',
        'display_text',
        'email',
        'country',
        'photo',
        'biography',
        'password',
        'slug',
        'template_data'
    ];

    protected $casts = [
        'date' => 'datetime',
        'price' => 'float',
        'active' => 'boolean',
        'type' => TemplateEnum::class,
        'options' => 'array',
        'metadata' => 'array',
        'template_data' => 'array'
    ];

    protected static function newFactory(): TemplateFactory
    {
        return TemplateFactory::new();
    }

    public function getStatusAttribute(): string
    {
        return $this->active ? 'active' : 'inactive';
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

}
