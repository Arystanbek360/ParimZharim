<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Application\Actions;

use Modules\ParimZharim\Objects\Domain\Models\CategoryCollection;
use Modules\ParimZharim\Objects\Domain\Repositories\CategoryRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetUsableCategories extends BaseAction {

    public function __construct(
        private readonly CategoryRepository $categoryRepository
    )
    {}


    public function handle(): CategoryCollection {
        return $this->categoryRepository->getUsableCategories();
    }
}
