<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Errors;

use Illuminate\Http\Request;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Tests\TestCase;

class ValidationErrorTest extends TestCase
{
    public function testErrorMessage(): void
    {
        $error_1 = new ValidationError();
        $error_2 = new ValidationError("Test error message");

        $this->assertEquals("Domain Error: Ошибка валидации", $error_1->getMessage());
        $this->assertEquals("Domain Error: Test error message", $error_2->getMessage());
        $this->assertEquals(422, $error_1->getStatusCode());
        $this->assertEquals(422, $error_2->getStatusCode());
    }

    public function testErrorRendering(): void
    {
        $error = new ValidationError("Test error message");
        $request = Request::create('/test', 'GET');
        $response = $error->render($request);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Test error message', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'), 'Content-Type is not application/json');
    }
}
