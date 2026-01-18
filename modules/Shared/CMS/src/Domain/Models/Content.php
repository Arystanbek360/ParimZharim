<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\CMS\Database\Factories\ContentFactory;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class Content
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property int|null $category_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 *
 */
class Content extends BaseModel {
    use HasFactory, SoftDeletes;

    protected $table = 'cms_contents';

    protected $fillable = [
        'title',
        'slug',
        'content',
    ];

    /**
     * @return ContentFactory
     */
    protected static function newFactory(): ContentFactory
    {
        return ContentFactory::new();
    }

    public function category(): ?BelongsTo
    {
        return $this->belongsTo(ContentCategory::class, 'category_id');
    }
}
