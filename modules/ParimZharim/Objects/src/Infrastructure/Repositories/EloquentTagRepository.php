<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Infrastructure\Repositories;


use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\ParimZharim\Objects\Domain\Models\TagCollection;
use Modules\ParimZharim\Objects\Domain\Repositories\TagRepository;
use Modules\Shared\Core\Infrastructure\BaseRepository;

class EloquentTagRepository extends BaseRepository implements TagRepository {

    public function getUsableTags(): TagCollection
    {
        $tags = Tag::whereHas('serviceObjects')
            ->where('is_visible_to_customers', '=', true)
            ->get();

        return new TagCollection($tags->all());
    }
}
