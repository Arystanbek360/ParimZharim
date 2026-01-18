<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Hidden;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
class CreateOrderForServiceObjectAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {

    }


    public function fields(NovaRequest $request): array
    {
        return [
            Hidden::make('Resource ID', 'resourceId')
                ->readonly()->default(function ($request) {
                return $request->resourceId;
            }),
        ];
    }

}
