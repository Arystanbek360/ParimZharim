<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\Payment\Application\Actions\CancelPayment;
use Throwable;

class CancelPaymentAdminAction extends BaseAdminAction
{

    public function handle(ActionFields $fields, Collection $models): void
    {
        foreach ($models as $model) {
            try {
                CancelPayment::make()->handle($model);
                $this->markAsFinished($model);
            } catch (Throwable $e) {
                $this->markAsFailed($model, $e->getMessage());
            }
        }
    }

    public function name(): string
    {
        return 'Отменить платеж';
    }

}
