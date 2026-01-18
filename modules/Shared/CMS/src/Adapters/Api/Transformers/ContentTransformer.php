<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Adapters\Api\Transformers;

use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\Core\Adapters\Api\BaseTransformer;
use Modules\Shared\Core\Application\BaseDTO;
use Modules\Shared\Core\Domain\BaseModel;
use Modules\Shared\Core\Domain\BaseValueObject;

class ContentTransformer extends BaseTransformer
{

    public function transform(Content|BaseDTO|BaseValueObject|BaseModel|array $data)
    {
        return [
            'id'   => $data->id,
            'title' => $data->title,
            'slug' => $data->slug,
            'content' => $data->content
        ];
    }
}
