<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Services;

use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;

class MobizoneSmsService extends BaseService implements SmsService {
    /**
     * @throws SMSServiceCommunicationError
     * @throws InvalidSMSServiceCredentials
     */
    public function send(string $phone, string $message): void
    {
        // Чистка номера телефона от символов. Только цифры, только хардкор.
        $phone = str_replace([' ', '+', '-', '(', ')'], '', $phone);

        // Если в переменных окружения есть конфигурационные данные, то отправляем запрос на отправку
        if (config('app.idm_mobizone_token')) {
            $response = Http::get("https://api.mobizon.kz/service/message/sendsmsmessage?recipient=$phone&text=$message&apiKey=" . config('app.idm_mobizone_token'));
            $responseJson = $response->json();
           // Log::info('response: ' . $response->body());
            if ($responseJson['code'] != 0) {
                throw new SMSServiceCommunicationError($responseJson['message']);
            }
            Log::info('SMS mobizon sended. Phone: ' . $phone . '. Text: ' . $message);
        } else {
          //  Log::error('SMS mobizone sending error');
            throw new InvalidSMSServiceCredentials('Invalid SMS service credentials');
        }
    }
}
