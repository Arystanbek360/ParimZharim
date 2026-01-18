<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Shared\CMS\Adapters\Api\Transformers\ContentTransformer;
use Modules\Shared\CMS\Application\Actions\GetContentBySlug;
use Modules\Shared\CMS\Application\Actions\GetContentCategoryById;
use Modules\Shared\CMS\Application\Actions\GetContentsByCategory;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;

class ContentApiController extends BaseApiController
{

    /**
     * Handle the API request to fetch content by its slug.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function getContentBySlug(Request $request): JsonResponse
    {
        $slug = $request->get('slug');  // Assuming the slug is sent as a query parameter

        if (!$slug) {
            return $this->respondError('Slug is required', 400);
        }

        $content = GetContentBySlug::make()->handle($slug);

        if (!$content) {
            return $this->respondNotFound('Content not found');
        }

        $this->setTransformer(new ContentTransformer());
        return $this->respondWithTransformer($content);
    }

    /**
     * Handle the API request to fetch content by its slug.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function getContentByCategory(Request $request): JsonResponse
    {
        $category_id = $request->get('category_id');  // Assuming the slug is sent as a query parameter

        if (!$category_id) {
            return $this->respondError('Требуется идентификатор категории', 400);
        }

        $category = GetContentCategoryById::make()->handle((int)$category_id);
        if (!$category) {
            return $this->respondNotFound('Категория не найдена');
        }
        $contents = GetContentsByCategory::make()->handle((int)$category_id);

        $this->setTransformer(new ContentTransformer());
        return $this->respondWithTransformer($contents);
    }

    private function setTransformer($transformer): void
    {
        $this->transformer = $transformer;
    }
}
