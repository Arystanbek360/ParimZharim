<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Admin\Resources;

use Dniccum\PhoneNumber\PhoneNumber;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Profile\Adapters\Admin\Actions\ChangePasswordAdminAction;
use Modules\ParimZharim\Profile\Domain\Models\Employee;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources\RoleAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class EmployeeAdminResource extends BaseAdminResource
{
    public static string $model = Employee::class;

    public static $title = 'name';

    public static $search = [
        'name',
        'phone',
        'job_title'
    ];

    public static function label(): string
    {
        return 'Сотрудник';
    }

    public function fields(NovaRequest $request)
    {
        return [
            ID::make(),
            Text::make('Имя Фамилия', 'name')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Text::make('Должность', 'job_title')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            PhoneNumber::make('Телефон', 'phone')
                ->sortable()
                ->format('+###########')
                ->placeholder('Введите номер телефона')
                ->disableValidation()
                ->rules('nullable', 'max:20')
                ->creationRules('unique:profile_employees,phone')
                ->updateRules('unique:profile_employees,phone,{{resourceId}}'),
            Text::make('Email', 'email')->sortable()
                ->rules('nullable', 'email', 'max:254'),

            BelongsToMany::make('Роли', 'roles', RoleAdminResource::class)
                ->fields(function () {
                    return [
                        Hidden::make('Тип модели', 'model_type')->resolveUsing(function () {
                            return User::class;
                        }),
                    ];
                }),

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

    public function actions(NovaRequest $request): array
    {
        return [
            (new ChangePasswordAdminAction())->canSee(function (NovaRequest $request) {
                if ($request instanceof ActionRequest) {
                    return true;
                }
                return $request->user()->can('changePassword', $this->resource);
            })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('changePassword', $resource);
                })
                ->exceptOnIndex()
                ->showInline()
                ->confirmText('Вы уверены, что хотите изменить пароль сотрудника?')
                ->size('lg')
                ->confirmButtonText('Изменить пароль'),
        ];
    }
}
