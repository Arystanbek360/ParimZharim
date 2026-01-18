<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Admin\Metrics;

use Laravel\Nova\Metrics\Progress;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\ProgressResult;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetPhoneVerificationCodesCountForUser;
use Illuminate\Support\Carbon;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;

class PhoneVerificationCodesLimitForUserPhone extends Progress
{
    /**
     * Calculate the value of the metric.
     *
     * @param NovaRequest $request
     * @return ProgressResult
     * @throws UserNotFound
     */
    public function calculate(NovaRequest $request): ProgressResult
    {
        if(isset($request->resourceId) && $request->resourceId != null)
        {
            $code = PhoneVerificationCode::find($request->resourceId);
        }

        /** @var PhoneVerificationCode $code */
        $userId = $code->user_id;

        $sentToday = GetPhoneVerificationCodesCountForUser::make()->handle((string)$userId);

        $totalLimit = config('app.idm_phone_verification_code_rate_limit_per_user_per_day', 15);

        return $this->result($sentToday, $totalLimit)->avoid();
    }

    /**
     * Get the name of the metric.
     *
     * @return string
     */
    public function name(): string
    {
        return 'Отправлено SMS-кодов из лимита для пользователя за 24 часа';
    }
}


