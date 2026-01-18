<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Tests\Domain\Errors;

use Modules\Shared\IdentityAndAccessManagement\Domain\Errors\UserWithPhoneAlreadyExists; // Обратите внимание на регистр I
use Modules\Shared\IdentityAndAccessManagement\Tests\TestCase;
use Illuminate\Http\Request;

class UserWithPhoneAlreadyExistsTest extends TestCase
{
    private string $testPhone = '1234567890';

    public function testRender(): void
    {
        $error = new UserWithPhoneAlreadyExists($this->testPhone); // Обратите внимание на регистр I
        $request = Request::create('/test', 'GET');

        $response = $error->render($request);

        $this->assertTrue($response->headers->has('Content-Type'), 'Header Content-Type is missing');
        $this->assertEquals('application/json', $response->headers->get('Content-Type'), 'Content-Type is not application/json');
    }

    public function testGetStatusCode(): void
    {
        $error = new UserWithPhoneAlreadyExists($this->testPhone); // Обратите внимание на регистр I
        $this->assertEquals(500, $error->getStatusCode());
    }

    public function testGetHeaders(): void
    {
        $error = new UserWithPhoneAlreadyExists($this->testPhone); // Обратите внимание на регистр I
        $this->assertEquals(['Content-Type' => 'application/json'], $error->getHeaders());
    }
}
