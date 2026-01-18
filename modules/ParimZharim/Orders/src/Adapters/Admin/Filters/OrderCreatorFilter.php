<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\ParimZharim\Orders\Domain\Models\OrderCreator;
use Modules\Shared\Core\Adapters\Admin\Filters\BaseAdminSelectFilter;

class OrderCreatorFilter extends BaseAdminSelectFilter
{
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value): Builder
    {

       if ($value === null) {
            return $query;
        }

        $creator = OrderCreator::query()
            ->whereIn('id', $this->getEligibleCreatorIds())
            ->where('name', $value)
            ->get();

        return $query->whereIn('creator_id', $creator->pluck('id'));
    }

    /**
     * Get the filter's available options.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request): array
    {
        // Используйте числовой ID как ключ, а имя как значение
        return OrderCreator::whereIn('id', $this->getEligibleCreatorIds())
            ->pluck('name', 'id')
            ->mapWithKeys(fn ($name, $id) => [(string) $id => $name])
            ->toArray();
    }


    /**
     * Get IDs of eligible creators (employees with active orders).
     *
     * @return array
     */
    protected function getEligibleCreatorIds(): array
    {
        return OrderCreator::query()
            // Ограничиваем выборку только сотрудниками, которые не удалены
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('profile_employees')
                    ->whereColumn('profile_employees.user_id', 'idm_users.id')
                    ->whereNull('profile_employees.deleted_at');
            })
            // Проверяем, есть ли у этих сотрудников активные заказы
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('orders_orders')
                    ->whereColumn('orders_orders.creator_id', 'idm_users.id')
                    ->whereNull('orders_orders.deleted_at');
            })
            // Собираем только IDs, которые удовлетворяют обоим условиям
            ->pluck('id')
            ->toArray();
    }


    /**
     * The name of the filter.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Администратор';
    }
}
