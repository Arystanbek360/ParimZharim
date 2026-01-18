<?php declare(strict_types=1);

namespace Modules\Shared\CMS\Application\Actions;

use Modules\Shared\CMS\Domain\Models\Content;
use Modules\Shared\CMS\Domain\Models\ContentCategory;
use Modules\Shared\CMS\Domain\Models\ContentCollection;
use Modules\Shared\CMS\Domain\Repositories\ContentRepository;
use Modules\Shared\CMS\Infrastructure\Repositories\EloquentContentRepository;
use Modules\Shared\Core\Application\BaseAction;


class GetContentCategoryById extends BaseAction
{
    public function __construct(private readonly ContentRepository $contentRepository)
    {
    }
    public function handle(int $categoryId): ?ContentCategory
    {
        return $this->contentRepository->getContentCategoryById($categoryId);
    }
}
