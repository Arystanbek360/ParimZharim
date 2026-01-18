<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Domain\Repositories;

use Modules\Shared\CMS\Domain\Models\ContentCategory;
use Modules\Shared\CMS\Domain\Models\ContentCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\CMS\Domain\Models\Content;

interface ContentRepository extends BaseRepositoryInterface {

    public function getContentBySlug(string $slug): ?Content;

    public function getContentsByCategory(int $categoryId): ?ContentCollection;

    public function getContentCategoryById(int $categoryId): ?ContentCategory;

}
