<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Actions;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Http\Requests\NovaRequest;
use Lednerb\ActionButtonSelector\ShowAsButton;
use Modules\Shared\Core\Adapters\Admin\BaseAdminAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\DeletePhoneVerificationCodesForPeriod;

class DeletePhoneVerificationCodesForPeriodAdminAction extends BaseAdminAction
{
    use ShowAsButton;
    public function handle(ActionFields $fields, Collection $models): void
    {
        DeletePhoneVerificationCodesForPeriod::make()->handle((string)$fields->dateFrom, (string)$fields->dateTo);
    }

    public function name(): string
    {
        return 'Удалить SMS-коды верификации за прошедший период';
    }

    /**
     * Определяем поля, которые будут отображаться в форме действия
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        return [
            Date::make('Дата начала', 'dateFrom')
                ->rules('required', 'date'),

            Date::make('Дата окончания', 'dateTo')
                ->rules('required', 'date'),
        ];
    }
}
