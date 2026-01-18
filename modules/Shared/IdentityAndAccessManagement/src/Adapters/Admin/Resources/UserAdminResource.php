<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources;

use Illuminate\Validation\Rules;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class UserAdminResource extends BaseAdminResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<User>
     */
    public static string $model = User::class;


    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'Пользователи';
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name', 'email',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        $mainFields = [
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

            Password::make('Пароль', 'password')
                ->onlyOnForms()
                ->creationRules('required', Rules\Password::defaults())
                ->updateRules('nullable', Rules\Password::defaults()),
        ];

        $roleFields = [
            BelongsToMany::make('Роли', 'roles', RoleAdminResource::class)
        ];

        return array_merge($mainFields, $roleFields);
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
