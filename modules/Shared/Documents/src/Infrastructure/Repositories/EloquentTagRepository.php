<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Infrastructure\Repositories;

use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Domain\Models\TagCollection;
use Modules\Shared\Documents\Domain\Repositories\TagRepository;

class EloquentTagRepository implements TagRepository
{
    public function saveTag(Tag $tag): void
    {
        $tag->save();
    }

    public function getTagByIds(array $ids): TagCollection
    {
        $collection = new TagCollection();

        foreach ($ids as $id) {
            $tag = Tag::find($id);
            if ($tag) {
                $collection->add($tag);
            }
        }

        return $collection;
    }

    public function getTags(int $limit = 100, int $offset = 0): TagCollection
    {
        $tags = Tag::limit($limit)->offset($offset)->get();

        $collection = new TagCollection();

        foreach ($tags as $tag) {
            if ($tag) {
                $collection->add($tag);
            }
        }

        return $collection;
    }
}
