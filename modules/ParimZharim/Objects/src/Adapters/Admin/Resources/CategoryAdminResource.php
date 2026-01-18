<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Admin\Resources;

use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Objects\Domain\Models\Category;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class CategoryAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = Category::class;

    public static $title = 'name';

    public static $search = [
        'name'
    ];

    public static function label(): string
    {
        return 'Категория';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),
            Text::make('Наименование Категории', 'name')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Textarea::make('Описание категории', 'description')->hideFromIndex()
                ->rules('required', 'max:255'),
            Image::make('Изображение', 'image')
                ->path(config('filesystems.folder').'/categories')
                ->rules('nullable', 'image', 'max:1024')
                ->withMeta(['value' => 'max. size 1MB'])
                ->creationRules('required')
                ->deletable(false),
            Boolean::make('Видимость для клиента', 'is_visible_to_customers')->sortable(),
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
        return [];
    }
}
