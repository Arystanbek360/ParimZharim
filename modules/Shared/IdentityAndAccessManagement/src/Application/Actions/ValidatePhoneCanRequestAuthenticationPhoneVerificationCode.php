<?php

declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;

class ValidatePhoneCanRequestAuthenticationPhoneVerificationCode extends BaseAction
{

    public function __construct(
        private readonly PhoneVerificationCodeRepository $phoneVerificationCodeRepository
    ) {}

    /**
     * @throws PhoneVerificationCodeRateLimitError
     */
    public function handle(string $phone): void
    {
        // Проверяем лимиты на отправку кодов для номера телефона в рамках интервала между кодами для одного номера
        $this->validateIntervalRateLimitForPhone($phone);

        // Проверяем лимиты на отправку кодов для номера телефона за сутки
        $this->validateDailyRateLimitForPhone($phone);

        // Проверяем лимиты на отправку кодов для всех номеров телефона
        $this->validateDailyRateLimitsForAllPhones();

    }

    /**
     * @throws PhoneVerificationCodeRateLimitError
     */
    private function validateIntervalRateLimitForPhone(string $phone): void
    {
        // Находим запись по номеру телефона
        $lastCodeEntry = $this->phoneVerificationCodeRepository->findLastByPhone($phone);

        // Если запись есть и ещё не прошло n секунд - кидаем исключение
        if ($lastCodeEntry != null) {
            $interval = (int) now()->diffInSeconds($lastCodeEntry->created_at, true);
            if ($interval < config('app.idm_phone_verification_code_send_interval_limit')) {
                throw new PhoneVerificationCodeRateLimitError(
                    'You can only send one PHONE_VERIFICATION_CODE every ' .
                    config('app.idm_phone_verification_code_send_interval_limit') .
                    ' seconds. Please try again later.'
                );
            }
        }
    }

    /**
     * @throws PhoneVerificationCodeRateLimitError
     */
    private function validateDailyRateLimitForPhone(string $phone): void
    {
        // Если количество записей по номеру телефона за сутки больше, чем лимит - кидаем исключение
        if ($this->phoneVerificationCodeRepository->countAllByPhonePerDay($phone) >= config('app.idm_phone_verification_code_rate_limit_per_user_per_day')) {
            throw new PhoneVerificationCodeRateLimitError(
                'You can send only ' .
                config('app.idm_phone_verification_code_rate_limit_per_user_per_day') .
                ' sms per day. Please try again later.'
            );
        }
    }

    /**
     * @throws PhoneVerificationCodeRateLimitError
     */
    private function validateDailyRateLimitsForAllPhones(): void
    {
        // Если количество записей за сутки больше, чем лимит - кидаем исключение
        if ($this->phoneVerificationCodeRepository->countAllPerDay() >= config('app.idm_phone_verification_code_rate_limit_per_all_users_per_day')) {
            throw new PhoneVerificationCodeRateLimitError(
                'You have a limited number of SMS messages. Please try again later.'
            );
        }
    }
}
