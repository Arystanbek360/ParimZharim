<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Service;

class ServiceAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = Service::class;

    /**
     * The single value that should be used to represent the resource in the UI.
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'name', 'description'
    ];

    public static function label(): string
    {
        return 'Услуга';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Наименование', 'name')
                ->sortable()
                ->rules('required', 'max:255'),

            Textarea::make('Описание', 'description')
                ->sortable()
                ->rules('nullable', 'max:1000'),

            Number::make('Цена', 'price')
                ->sortable()
                ->rules('required', 'numeric'),

            Boolean::make('Включен', 'is_active')
                ->sortable()
                ->filterable(),

            BelongsTo::make('Категория', 'serviceCategory', ServiceCategoryAdminResource::class)
                ->sortable()
                ->filterable()
                ->showCreateRelationButton(),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
    {
        return [];
    }
}
