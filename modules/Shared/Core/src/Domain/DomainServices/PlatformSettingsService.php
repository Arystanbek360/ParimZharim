<?php declare(strict_types=1);

namespace Modules\Shared\Core\Domain\DomainServices;

use Modules\Shared\Core\Domain\Errors\CannotAddSettingsAfterInitialization;

class PlatformSettingsService
{
    protected array $settings = [];
    protected bool $initialized = false;

    /**
     * @throws CannotAddSettingsAfterInitialization
     */
    public function addSetting(string $key, $value): void
    {
        if ($this->initialized) {
            throw new CannotAddSettingsAfterInitialization();
        }

        $this->settings[$key] = $value;
    }

    public function getSetting(string $key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Add type-to-model mappings.
     *
     * @throws CannotAddSettingsAfterInitialization
     */
    public function addTypeModelMappings(array $mappings): void
    {
        if ($this->initialized) {
            throw new CannotAddSettingsAfterInitialization();
        }

        if (!isset($this->settings['type_model_mappings'])) {
            $this->settings['type_model_mappings'] = [];
        }

        // Merge the new mappings with existing ones
        $this->settings['type_model_mappings'] = array_merge_recursive(
            $this->settings['type_model_mappings'],
            $mappings
        );
    }

    /**
     * Get all type-to-model mappings.
     */
    public function getTypeModelMappings(): array
    {
        return $this->settings['type_model_mappings'] ?? [];
    }

    public function lockSettings(): void
    {
        $this->initialized = true;
    }
}
