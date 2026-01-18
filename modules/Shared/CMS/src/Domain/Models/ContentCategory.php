<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Shared\Core\Domain\BaseModel;

/**
 * Class ContentCategory
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class ContentCategory extends BaseModel
{
    use HasFactory, SoftDeletes;

    protected $table = 'cms_content_categories';

    protected $fillable = [
        'name',
        'description',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array', // Преобразование JSON в массив
    ];
}
