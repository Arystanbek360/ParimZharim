<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\ParimZharim\Orders\Domain\Models\OrderSource;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminBooleanFilter;

class OrderSourceFilter extends BaseAdminBooleanFilter
{
    public function apply(Request $request, $query, $value): Builder
    {
        // Filter only the sources that are set to true.
        $filteredSources = array_filter($value, function ($enabled) {
            return $enabled === true;
        });

        // Map the labels back to their enum values.
        $sourcesToFilter = array_map(function ($label) {
            return OrderSource::fromLabel($label);
        }, array_keys($filteredSources));

        // Remove any null values which might have been added if the label did not match any case
        $sourcesToFilter = array_filter($sourcesToFilter, function ($source) {
            return !is_null($source);
        });

        if (!empty($sourcesToFilter)) {
            return $query->whereIn('metadata->source', $sourcesToFilter);
        }

        return $query;
    }

    public function options(Request $request): array
    {
        return array_map(function ($source) {
            return $source->label();
        }, OrderSource::cases());
    }

    /**
     * Set the name of the filter to be displayed in the Nova dashboard.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Источник заказа';
    }
}
