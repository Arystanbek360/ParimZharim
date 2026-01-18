<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Admin\Resources;

use Dniccum\PhoneNumber\PhoneNumber;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Profile\Adapters\Admin\Actions\DeleteCustomerAdminAction;
use Modules\ParimZharim\Profile\Domain\Models\Customer;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class CustomerAdminResource extends BaseAdminResource
{
    public static string $model = Customer::class;

    public static $title = 'name';

    public static $search = [
        'name',
        'phone',
    ];

    public static function label(): string
    {
        return 'Клиент';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),
            Text::make('Имя Фамилия', 'name')
                ->sortable()
                ->rules('required', 'max:255'),
            PhoneNumber::make('Телефон', 'phone')
                ->sortable()
                ->format('+###########')
                ->placeholder('Введите номер телефона')
                ->disableValidation()
                ->rules('nullable', 'max:20')
                ->creationRules('unique:profile_customers,phone')
                ->updateRules('unique:profile_customers,phone,{{resourceId}}'),
            Date::make('Дата рождения', 'date_of_birth')->sortable()
                ->filterable(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new DeleteCustomerAdminAction())
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('deleteCustomerProfile', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('deleteCustomerProfile', $resource);
                })
                ->confirmText('Вы уверены что хотите удалить данные клиента?')
                ->confirmButtonText('Удалить')
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function lenses(NovaRequest $request): array
    {
        return [];
    }
}
