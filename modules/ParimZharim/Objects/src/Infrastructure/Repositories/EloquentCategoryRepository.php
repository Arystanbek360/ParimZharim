<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Infrastructure\Repositories;

use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;

class EloquentCategoryRepository extends BaseRepository implements CategoryRepository {
    public function getUsableCategories(): CategoryCollection
    {
        // Fetch categories that are associated with serviceObjects and are visible to customers, sorted by id
        $categories = Category::whereHas('serviceObjects')
            ->where('is_visible_to_customers', '=', true)
            ->orderBy('id') // Add this line to sort by id
            ->get();

        return new CategoryCollection($categories->all());
    }
}
