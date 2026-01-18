<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\Payment\Application\Actions\MarkFailedPaymentShown;

class MarkFailedPaymentShownAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            MarkFailedPaymentShown::make()->handle($model->id);
        }
    }

    public function name(): string
    {
        return 'Просмотрен';
    }

}
