<?php declare(strict_types=1);

namespace Modules\Shared\Core\Adapters\Admin;

use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;

class BaseAdminResourceLens extends Lens
{

    public static function query(LensRequest $request, $query)
    {
        // TODO: Implement query() method.
    }

    public function fields(NovaRequest $request)
    {
        // TODO: Implement fields() method.
    }
}
