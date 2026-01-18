<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminBooleanFilter;

class OrderObjectCategoryFilter extends BaseAdminBooleanFilter
{
    public $component = 'boolean-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value): Builder
    {
        // Extract category IDs that are marked as true (active)
        $selectedCategories = array_keys(array_filter($value, fn($val) => $val));

        // Convert to integers and filter query if not empty
        if (!empty($selectedCategories)) {
            // Получаем ID категорий по их названиям
            $selectedCategoryIds = Category::whereIn('name', $selectedCategories)
                ->pluck('id')
                ->toArray();

            // Фильтруем основной запрос с помощью ID категорий
            $query->whereHas('orderableServiceObject', function ($subQuery) use ($selectedCategoryIds) {
                $subQuery->whereIn('category_id', $selectedCategoryIds);
            });
        }

        return $query;
    }


    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request): array
    {
        return Category::query()
            ->pluck('name', 'id')
            ->mapWithKeys(fn ($name, $id) => [(string) $id => $name])
            ->toArray();
    }


    /**
     * The name of the filter.
     *
     * @return string
     */
    public function name()
    {
        return 'Категория объекта';
    }
}
