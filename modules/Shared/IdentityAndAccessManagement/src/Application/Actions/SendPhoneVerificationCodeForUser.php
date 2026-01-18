<?php

declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Facades\Log;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Random\RandomException;
use function Sentry\captureException;

class SendPhoneVerificationCodeForUser extends BaseAction
{
    public function __construct(
        private readonly PhoneVerificationCodeRepository $phoneVerificationCodeRepository,
        private readonly SmsService                      $smsService
    ) {
    }

    /**
     * @throws InvalidInputData
     * @throws RandomException
     * @throws PhoneVerificationCodeRateLimitError
     */
    public function handle(User $user, string $phone): void
    {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($phone);

        // validate that the user can request a phone verification code by rate limit
        ValidatePhoneCanRequestAuthenticationPhoneVerificationCode::make()->handle($phone);

        // Determine if the phone number is a test number
        $isTestNumber = preg_match('/^\+7000000\d\d\d\d$/', $phone);

        // Create a new verification code or set a specific code for a defined range
        if ($isTestNumber) {
            $code = 666666; // Set the code to 666666 for numbers within +70000000000 to +70000009999
        } else {
            $code = random_int(100000, 999999);
        }
        $this->phoneVerificationCodeRepository->markAllOldCodesForPhoneAsExpired($phone);
        $this->phoneVerificationCodeRepository->create($user, $phone, $code);

        // Send the code to the user if the phone number is not a test number
        if (!$isTestNumber) {
            try {
                $this->smsService->send($phone, config('app.idm_sms_sender_name'). ": Код подтверждения: $code");
            } catch (InvalidSMSServiceCredentials $e) {
                Log::error("Invalid SMS service credentials");
                Log::error($e->getMessage());
                captureException($e);
            } catch (SMSServiceCommunicationError $e) {
                Log::error("SMS service communication error");
                Log::error($e->getMessage());
                captureException($e);
            }
        }
    }
}
