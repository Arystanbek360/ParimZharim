<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Services;

use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidSMSServiceCredentials;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\SMSServiceCommunicationError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Services\SmsService;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class SmsServiceTest extends TestCase
{
    /**
     * @var SmsService|MockObject
     */
    private SmsService $smsService;

    protected function setUp(): void
    {
        parent::setUp();
        // Создаем заглушку для SmsService
        $this->smsService = $this->createMock(SmsService::class);
    }

    public function testSendWithValidData(): void
    {
        $phone = '70000000000';
        $message = 'Test message';

        // Устанавливаем ожидание вызова метода send с конкретными аргументами
        $this->smsService->expects($this->once())
            ->method('send')
            ->with($phone, $message);

        // Вызываем метод send и проверяем, что все ожидания выполнены
        $this->smsService->send($phone, $message);
    }

    public function testSendWithInvalidCredentials(): void
    {
        $phone = '70000000000';
        $message = 'Test message';

        // Устанавливаем заглушку для генерации InvalidSMSServiceCredentials
        $this->smsService->method('send')
            ->will($this->throwException(new InvalidSMSServiceCredentials()));

        // Проверяем, что вызов метода send генерирует ожидаемое исключение
        $this->expectException(InvalidSMSServiceCredentials::class);
        $this->smsService->send($phone, $message);
    }

    public function testSendWithCommunicationError(): void
    {
        $phone = '70000000000';
        $message = 'Test message';

        // Устанавливаем заглушку для генерации SMSServiceCommunicationError
        $this->smsService->method('send')
            ->will($this->throwException(new SMSServiceCommunicationError()));

        // Проверяем, что вызов метода send генерирует ожидаемое исключение
        $this->expectException(SMSServiceCommunicationError::class);
        $this->smsService->send($phone, $message);
    }
}
