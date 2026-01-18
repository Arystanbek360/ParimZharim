<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Application\Actions;

use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;
use Modules\Shared\Core\Application\BaseAction;

class GetUsableTags extends BaseAction {

    public function __construct(
        private readonly TagRepository $tagRepository
    )
    {}


    public function handle(): TagCollection {
        return $this->tagRepository->getUsableTags();
    }
}
