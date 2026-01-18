<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Domain\Repositories;

use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\Shared\Core\Domain\BaseRepositoryInterface;

interface TagRepository extends BaseRepositoryInterface {

    public function getUsableTags(): TagCollection;
}
