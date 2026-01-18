<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Api\Transformers;

use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;
use Illuminate\Support\Facades\Storage;

class CategoryTransformer extends BaseTransformer
{

    public function transform(Category|BaseDTO|BaseValueObject|BaseModel|array $data): array
    {
        return [
            'type' => $data->id,
            'name' => $data->name,
            'photo' => $data->image ? asset(Storage::disk(config('filesystems.default'))->url($data->image)) : null,
        ];
    }
}
