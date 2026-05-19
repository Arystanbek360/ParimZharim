<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\ParimZharim\Orders\Application\Actions\CancelTechnicalReserveForOrderableServiceObject;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;

class CancelTechnicalReserveForOrderableServiceObjectAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            CancelTechnicalReserveForOrderableServiceObject::make()->handle((int)$model->id);
        }
    }

    public function fields(NovaRequest $request): array
    {
        return [];
    }

    public function name(): string
    {
        return 'Снять технический резерв';
    }

}
