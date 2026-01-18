<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Resources;

use Illuminate\Support\Carbon;
use Laravel\Nova\Exceptions\HelperNotSupported;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\ActionRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Modules\Shared\Core\Adapters\Admin\BaseAdminResource;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Actions\DeletePhoneVerificationCodesForPeriodAdminAction;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Metrics\PhoneVerificationCodesLimitForAll;
use Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Metrics\PhoneVerificationCodesLimitForUserPhone;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\Role;

/**
 * @property Carbon $expires_at
 */
class PhoneVerificationCodeAdminResource extends BaseAdminResource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<Role>
     */
    public static string $model = PhoneVerificationCode::class;

    /**
     * The model display name
     */
    public static function label(): string
    {
        return 'SMS-коды верификации';
    }

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'label';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'user.id',
        'user.name',
        'phone',
    ];

    public static $polling = true;
    public static $pollingInterval = 30;

    /**
     * Get the fields displayed by the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function fields(NovaRequest $request): array
    {
        Carbon::setLocale('ru');
        return [
            ID::make()->sortable(),

            BelongsTo::make('Пользователь', 'user', UserAdminResource::class)
                ->searchable()
                ->sortable()
                ->readonly(),

            Text::make('Телефон', 'phone')
                ->readonly(),

            Text::make('Код', 'code')
                ->readonly(),

            Text::make('Действителен до', 'expires_at')
                ->displayUsing(function ($value) {
                    return $this->convertUtcToTimezone($value)->isoFormat('dd,  D MMMM YYYY HH:mm');
                })
                ->sortable()
                ->readonly(),

            Boolean::make('Действителен', function () {
                return $this->expires_at >= Carbon::now();
            })->readonly(),

            Text::make('Создан', 'created_at')
                ->displayUsing(function ($value) {
                    return $this->convertUtcToTimezone($value)->isoFormat('dd,  D MMMM YYYY HH:mm');
                })
                ->sortable()
                ->readonly(),
        ];
    }

    private function convertUtcToTimezone(?Carbon $value): ?Carbon
    {
        $timezone = 'Asia/Almaty';
        return $value->setTimezone($timezone);
    }

    /**
     * Get the cards available for the request.
     *
     * @param NovaRequest $request
     * @return array
     * @throws HelperNotSupported
     */
    public function cards(NovaRequest $request): array
    {
            return [
                (new PhoneVerificationCodesLimitForUserPhone())->onlyOnDetail()->width('full')->refreshWhenActionsRun(),
                (new PhoneVerificationCodesLimitForAll())->width('full')->refreshWhenActionsRun()
            ];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function actions(NovaRequest $request): array
    {
        return [
            (new DeletePhoneVerificationCodesForPeriodAdminAction())
                ->standalone()
                ->showAsButton()
                ->canSee(function (NovaRequest $request) {
                    if ($request instanceof ActionRequest) {
                        return true;
                    }
                    return $request->user()->can('managePhoneVerificationCode', $this->resource);
                })
                ->canRun(function (NovaRequest $request, $resource) {
                    return $request->user()->can('managePhoneVerificationCode', $resource);
                })->confirmText('Вы уверены, что хотите удалить все недействительные SMS-коды верификации за прошедший период?')
            ->confirmButtonText('Да, удалить')
            ->size('3xl')
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }
}
