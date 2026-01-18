<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Infrastructure\Errors;

use Illuminate\Http\Request;
use Modules\Shared\Documents\Infrastructure\Errors\CantRecreateModelError;
use Modules\Shared\Documents\Tests\TestCase;

class CantRecreateModelErrorTest extends TestCase
{
    public function testErrorMessage(): void
    {
        $error_1 = new CantRecreateModelError();
        $error_2 = new CantRecreateModelError("Не удалось воссоздать модель из базы данных");

        $this->assertEquals("Infrastructure Error: Не удалось воссоздать модель из базы данных", $error_1->getMessage());
        $this->assertEquals("Infrastructure Error: Не удалось воссоздать модель из базы данных", $error_2->getMessage());
        $this->assertEquals(422, $error_1->getStatusCode());
        $this->assertEquals(422, $error_2->getStatusCode());
    }

    public function testErrorRendering(): void
    {
        $error = new CantRecreateModelError("Failure on recreating Document model");
        $request = Request::create('/test', 'GET');
        $response = $error->render($request);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Failure on recreating Document model', $response->getContent());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'), 'Content-Type is not application/json');
    }
}
