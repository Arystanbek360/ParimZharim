<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Services;

use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;

class SmscSmsService extends BaseService implements SmsService {

    /**
     * @throws SMSServiceCommunicationError
     * @throws InvalidSMSServiceCredentials
     */
    public function send(string $phone, string $message): void
    {
        // Чистка номера телефона от символов. Только цифры, только хардкор.
        $phone = str_replace([' ', '+', '-', '(', ')'], '', $phone);

        // Если номер телефона не начинается с 7, то пропускаем. Временный фикс для надежности, надо переделать на разрешение отправки в список стран.
        if (!str_starts_with($phone, '7')) {
            Log::info('SMS smsc sending error. Phone: ' . $phone . '. Message: ' . $message);
            return;
        }

        // Если в переменных окружения есть конфигурационные данные, то отправляем запрос на отправку. Всё логируем.
        if (config('app.idm_smsc_login') && config('app.idm_smsc_password')) {
            $response = Http::get("https://smsc.kz/sys/send.php?fmt=3&login=" . config('app.idm_smsc_login') . "&psw=" . config('app.idm_smsc_password') . "&phones=$phone&mes=$message");
            $responseJson = $response->json();
            if (isset($responseJson['error'])) {
                Log::error('error: ' . $responseJson['error']);
                throw new SMSServiceCommunicationError($responseJson['error']);
            }
            Log::info('SMS smsc sended. Phone: ' . $phone . '. Text: ' . $message);
        } else {
            Log::error('SMS smsc sending error');
            throw new InvalidSMSServiceCredentials('Invalid SMS service credentials');
        }
    }
}
