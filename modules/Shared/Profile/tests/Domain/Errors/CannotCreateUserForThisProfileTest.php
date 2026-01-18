<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Tests\Domain\Errors;


use Illuminate\Http\Request;
use Modules\Shared\Profile\Domain\Errors\CannotCreateUserForThisProfile;
use Modules\Shared\Profile\Tests\TestCase;

class CannotCreateUserForThisProfileTest extends TestCase
{
    /**
     * Проверка создания исключения с корректным сообщением.
     */
    public function testCannotCreateUserForThisProfileCreation(): void
    {
        $message = 'Test error message';
        $exception = new CannotCreateUserForThisProfile($message);

        $this->assertStringContainsString($message, $exception->getMessage());
        $this->assertEquals(500, $exception->getStatusCode());
        $this->assertEquals(['Content-Type' => 'application/json'], $exception->getHeaders());
    }

    /**
     * Проверка рендера ошибки в ответе.
     */
    public function testCannotCreateUserForThisProfileRender(): void
    {
        $message = 'Test error message';
        $exception = new CannotCreateUserForThisProfile($message);
        $request = Request::create('/test');

        $response = $exception->render($request);

        $this->assertEquals(500, $response->status());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertStringContainsString($message, json_decode($response->getContent(), true)['message']);
    }
}
