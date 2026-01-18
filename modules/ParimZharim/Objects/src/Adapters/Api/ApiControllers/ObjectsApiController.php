<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Modules\ParimZharim\Objects\Adapters\Api\Transformers\CategoryTransformer;
use Modules\ParimZharim\Objects\Adapters\Api\Transformers\TagTransformer;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableCategories;
use Modules\ParimZharim\Objects\Application\Actions\GetUsableTags;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;

class ObjectsApiController extends BaseApiController {

    /**
     * @throws InvalidDataTransformer
     */
    public function getCategories(): JsonResponse
    {
        $categories = GetUsableCategories::make()->handle();
        $this->setTransformer(new CategoryTransformer());
        return $this->respondWithTransformer($categories);
    }

    /**
     * @throws InvalidDataTransformer
     */
    public function getTags(): JsonResponse
    {
        $tags = GetUsableTags::make()->handle();
        $this->setTransformer(new TagTransformer());
        return $this->respondWithTransformer($tags);
    }

    private function setTransformer($transformer): void
    {
        $this->transformer = $transformer;
    }
}
