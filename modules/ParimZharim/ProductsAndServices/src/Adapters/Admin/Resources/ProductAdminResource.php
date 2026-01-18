<?php declare(strict_types=1);

namespace Modules\ParimZharim\ProductsAndServices\Adapters\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\ParimZharim\ProductsAndServices\Domain\Models\Product;

class ProductAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = Product::class;

    /**
     * The single value that should be used to represent the resource in the UI.
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     */
    public static $search = [
        'id', 'name'
    ];

    public static function label(): string
    {
        return 'Меню';
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

            Text::make('Название', 'name')
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

            Image::make('Изображение', 'image')
                ->path(config('filesystems.folder').'/products')
                ->sortable()
                ->creationRules('required', 'image', 'max:1024')
                ->withMeta(['value' => 'max. size 1MB']),

            BelongsTo::make('Категория меню', 'productCategory', ProductCategoryAdminResource::class)
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
