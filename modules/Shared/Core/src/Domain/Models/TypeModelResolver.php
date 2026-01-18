<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain\Models;

use Modules\Shared\Core\Domain\DomainServices\PlatformSettingsService;

class TypeModelResolver
{
    protected array $mappings;

    public function __construct(PlatformSettingsService $platformSettingsService)
    {
        // Retrieve mappings from PlatformSettingsService
        $this->mappings = $platformSettingsService->getTypeModelMappings();
    }

    /**
     * Get the model class for the given base model class and type.
     *
     * @param string $baseModelClass
     * @param string $type
     * @return string|null
     */
    public function getModelClass(string $baseModelClass, string $type): ?string
    {
        return $this->mappings[$baseModelClass][$type] ?? null;
    }
}
