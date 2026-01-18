<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Services;

use Modules\Shared\Core\Domain\BaseServiceInterface;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;

interface SmsService extends BaseServiceInterface {
    /**
     * @throws SMSServiceCommunicationError
     * @throws InvalidSMSServiceCredentials
     */
    public function send(string $phone, string $message): void;
}
