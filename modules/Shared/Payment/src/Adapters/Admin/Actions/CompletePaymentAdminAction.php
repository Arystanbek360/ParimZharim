<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\Payment\Application\Actions\CompletePayment;
use Throwable;

class CompletePaymentAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            try {
                CompletePayment::make()->handle($model);
            } catch (Throwable $e) {
                throw $e;
            }
        }
    }

    public function name(): string
    {
        return 'Завершить оплату';
    }

}
