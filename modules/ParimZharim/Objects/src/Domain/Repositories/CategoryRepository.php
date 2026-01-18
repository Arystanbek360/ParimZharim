<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Repositories;

use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface CategoryRepository extends BaseRepositoryInterface {

    public function getUsableCategories(): CategoryCollection;
}
