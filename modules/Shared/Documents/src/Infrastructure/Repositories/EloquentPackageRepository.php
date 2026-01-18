<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Infrastructure\Repositories;

use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Models\PackageCollection;
use Modules\Shared\Documents\Domain\Models\PackageQueryParams;
use Modules\Shared\Documents\Domain\Repositories\DocumentRepository;
use Modules\Shared\Documents\Domain\Repositories\PackageRepository;

class EloquentPackageRepository implements PackageRepository
{
    protected static function getDocumentRepository(): DocumentRepository
    {
        return app(DocumentRepository::class);
    }

    public function savePackage(Package $package): void
    {
        $package->save();
    }

    public function getPackageById(int $packageId): Package|null
    {
        return Package::find($packageId)->first();
    }

    public function saveDocumentInPackage(Document $document, Package $package): void
    {
        $document->package()->associate($package);
        self::getDocumentRepository()->saveDocument($document);
    }

    public function getPackageByQuery(PackageQueryParams $queryParams): PackageCollection|null
    {
        $query = Package::query();

        // Запрос where для 'name'
        if ($queryParams->name !== null) {
            $query->where('name', 'LIKE', "%{$queryParams->name}%");
        }

        // Запрос whereIn для 'types'
        if ($queryParams->types !== null) {
            $query->whereIn('type', $queryParams->types);
        }

        // Запрос whereIn для 'statuses'
        if ($queryParams->statuses !== null) {
            $query->whereIn('status', $queryParams->statuses);
        }

        // Запрос whereIn для 'ids'
        if ($queryParams->ids !== null) {
            $query->whereIn('id', $queryParams->ids);
        }

        // Запрос whereIn для 'creator_ids'
        if ($queryParams->creator_ids !== null) {
            $query->whereIn('creator_id', $queryParams->creator_ids);
        }

        // Запрос where для 'parent_package_id'
        if ($queryParams->parent_package_id !== null) {
            $query->where('parent_package_id', $queryParams->parent_package_id);
        }

        $packages = $query->get();

        if ($packages->isEmpty())
        {
            //throw new ModelNotFoundError("No packages found matching the given criteria.");
            return null;
        }

        return new PackageCollection($packages);
    }
}
