<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Api\Transformers;

use Illuminate\Support\Facades\Storage;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class TagTransformer extends BaseTransformer
{

    public function transform(Tag|BaseDTO|BaseValueObject|BaseModel|array $data): array
    {
        return [
            'id' => $data->id,
            'name' => $data->name,
            'img' => $data->image ? asset(Storage::disk(config('filesystems.default'))->url($data->image)) : null,
        ];
    }
}
