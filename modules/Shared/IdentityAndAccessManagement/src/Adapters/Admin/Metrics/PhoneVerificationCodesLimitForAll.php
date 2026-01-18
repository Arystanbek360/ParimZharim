<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Metrics;

use Laravel\Nova\Metrics\Progress;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\ProgressResult;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetPhoneVerificationCodesCountForAll;

class PhoneVerificationCodesLimitForAll extends Progress
{
    /**
     * Calculate the value of the metric.
     *
     * @param  NovaRequest  $request
     * @return ProgressResult
     */
    public function calculate(NovaRequest $request): ProgressResult
    {
        $sentToday = GetPhoneVerificationCodesCountForAll::make()->handle();

        $totalLimit = config('app.idm_phone_verification_code_rate_limit_per_all_users_per_day', 1500);

        return $this->result($sentToday, $totalLimit)->avoid();
    }

    /**
     * Get the name of the metric.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Отправлено SMS-кодов из общего лимита за 24 часа';
    }
}


