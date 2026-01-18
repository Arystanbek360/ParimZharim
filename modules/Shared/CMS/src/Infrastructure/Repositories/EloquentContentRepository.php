<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Infrastructure\Repositories;

use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\CMS\Domain\Models\ContentCategory;
use Modules\Shared\CMS\Domain\Repositories\ContentRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\CMS\Domain\Models\ContentCollection;



class EloquentContentRepository extends BaseRepository implements ContentRepository {
    public function getContentBySlug(string $slug): ?Content
    {
        return Content::where('slug', $slug)->first();
    }


    public function getContentsByCategory(int $categoryId): ?ContentCollection
    {
        $contents = Content::where('category_id', $categoryId)->get();
        return new ContentCollection($contents);
    }

    public function getContentCategoryById(int $categoryId): ?ContentCategory
    {
        return ContentCategory::find($categoryId);
    }
}
