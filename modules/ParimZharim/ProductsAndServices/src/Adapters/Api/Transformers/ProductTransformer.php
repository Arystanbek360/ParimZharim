<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers;

use Illuminate\Support\Facades\Storage;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class ProductTransformer extends BaseTransformer
{
    public function transform(Product|BaseDTO|BaseModel|BaseValueObject|array $data): array
    {
        return [
            'id'   => $data->id,
            'name' => $data->name,
            'description' => $data->description,
            'price' => $data->price,
            'photo' => $data->image ? asset(Storage::disk(config('filesystems.default'))->url($data->image)) : null,
            'category_id' => $data->product_category_id,
        ];
    }
}
