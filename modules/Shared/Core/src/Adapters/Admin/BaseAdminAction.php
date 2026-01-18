<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action as NovaAction;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;

/**
 * Class BaseAdminAction
 */
abstract class BaseAdminAction extends NovaAction
{
    use InteractsWithQueue, Queueable;

    abstract public function handle(ActionFields $fields, Collection $models);

    public function fields(NovaRequest $request): array
    {
        return [];
    }
}
