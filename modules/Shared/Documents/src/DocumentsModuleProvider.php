<?php declare(strict_types=1);

namespace Modules\Shared\Documents;

use Modules\Shared\Core\BaseModuleProvider;
use Modules\Shared\Documents\Domain\Repositories\DocumentRepository;
use Modules\Shared\Documents\Domain\Repositories\PackageRepository;
use Modules\Shared\Documents\Domain\Repositories\TagRepository;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentDocumentRepository;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentPackageRepository;
use Modules\Shared\Documents\Infrastructure\Repositories\EloquentTagRepository;

class DocumentsModuleProvider extends BaseModuleProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DocumentRepository::class, EloquentDocumentRepository::class);
        $this->app->bind(PackageRepository::class, EloquentPackageRepository::class);
        $this->app->bind(TagRepository::class, EloquentTagRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
