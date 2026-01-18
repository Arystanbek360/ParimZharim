<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Domain\Models\OrderCreator;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\UserAdminResource;

class OrderCreatorAdminResource extends UserAdminResource {
    public static string $model = OrderCreator::class;

    public static $title = 'name';

    public static $search = [
        'name',
        'email',
        'phone',
    ];

    public static function label(): string
    {
        return 'Заказы по управляющим';
    }

    public function fields(NovaRequest $request): array
    {

        // Если является сотрудником, возвращаем базовые и специфические поля
       $fields = [
           ID::make()->sortable(),

            Text::make('Имя', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email', 'email')
                ->sortable()
                ->rules('nullable', 'email', 'max:254')
                ->creationRules('unique:idm_users,email')
                ->updateRules('unique:idm_users,email,{{resourceId}}'),

            Text::make('Телефон', 'phone')
                ->sortable()
                ->rules('nullable', 'max:20')
                ->creationRules('unique:idm_users,phone')
                ->updateRules('unique:idm_users,phone,{{resourceId}}'),
           ];

        $fields[] = Text::make('Должность', 'job_title')
            ->sortable()
            ->hideWhenCreating()
            ->hideWhenUpdating()
            ->displayUsing(function () {
                return $this->resource->profile->job_title;
            });


        $fields[] = HasMany::make('Заказы', 'orders', OrderAdminResource::class);

        return $fields;
    }

    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        return $query->whereExists(function ($query) {
            // This subquery ensures the user is an employee by checking the 'profile_employees' table
            $query->select(DB::raw(1))
                ->from('profile_employees')
                ->whereColumn('profile_employees.user_id', 'idm_users.id')
                ->whereNull('profile_employees.deleted_at'); // Ensure the employee is not marked as deleted
        })
            ->whereExists(function ($query) {
                // This subquery checks if the user has any orders
                $query->select(DB::raw(1))
                    ->from('orders_orders')
                    ->whereColumn('orders_orders.creator_id', 'idm_users.id')
                    ->whereNull('orders_orders.deleted_at'); // Ensure the order is not marked as deleted
            });
    }



}
