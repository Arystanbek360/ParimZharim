<?php declare(strict_types=1);

namespace Modules\ParimZharim\Objects\Adapters\Admin\Resources;

use Devcraft\CustomJsonField\CustomJsonArray;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Objects\Domain\Models\Tag;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;

class TagAdminResource extends BaseAdminResource {

    /**
     * The model the resource corresponds to.
     */
    public static string $model = Tag::class;

    public static $title = 'name';

    public static $search = [
        'name'
    ];

    public static function label(): string
    {
        return 'Тег';
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(),
            Text::make('Наименование Тега', 'name')->sortable()
                ->filterable()
                ->rules('required', 'max:255'),
            Image::make('Изображение', 'image')
                ->path(config('filesystems.folder').'/tags')
                ->creationRules('required', 'image', 'mimetypes:image/svg+xml', 'max:100')
                ->updateRules('nullable', 'image', 'mimetypes:image/svg+xml')
                ->withMeta(['value' => 'max. size 100KB'])
                ->help('загрузите изображение в формате SVG')
                ->deletable(false),
            Boolean::make('Видимость для клиента', 'is_visible_to_customers')->sortable()
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
