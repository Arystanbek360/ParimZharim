<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Errors;

use Modules\Shared\IdentityAndAccessManagement\Domain\Errors\UserWithEmailAlreadyExists;
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Illuminate\Http\Request;

class UserWithEmailAlreadyExistsTest extends TestCase
{
    private string $testEmail = 'test@example.com';

    public function testRender(): void
    {
        $error = new UserWithEmailAlreadyExists($this->testEmail);
        $request = Request::create('/test', 'GET');

        $response = $error->render($request);

        // Проверяем наличие заголовка Content-Type и его значение
        $this->assertTrue($response->headers->has('Content-Type'), 'Header Content-Type is missing');
        $this->assertEquals('application/json', $response->headers->get('Content-Type'), 'Content-Type is not application/json');
    }

    public function testGetStatusCode(): void
    {
        $error = new UserWithEmailAlreadyExists($this->testEmail);
        $this->assertEquals(500, $error->getStatusCode());
    }

    public function testGetHeaders(): void
    {
        $error = new UserWithEmailAlreadyExists($this->testEmail);
        $this->assertEquals(['Content-Type' => 'application/json'], $error->getHeaders());
    }
}
