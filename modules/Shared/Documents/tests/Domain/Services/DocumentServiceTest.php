<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Services\DocumentService;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class DocumentServiceTest extends TestCase
{


    public function testCreateNewDocument(): void
    {
        // Создаем пользователя через фабрику
        $user = User::factory()->create();

        // Создаем документ, указывая пользователя как создателя
        $document = $this->makeAndGetTestDocument(['version_number' => 1, 'creator_id' => $user->id], false);

        // Сохраняем документ с использованием сервиса
        $createdDocument = DocumentService::createNewDocument($document);

        // Проверяем, что документ сохранен в базе данных
        $this->assertDatabaseHas('documents_documents', ['id' => $createdDocument->id]);
        $this->assertEquals(1, $createdDocument->version_number);
    }

    /**
     * @throws ValidationError
     */
    public function testCreateNewVersionOfDocument(): void
    {
        // Создаем пользователя и документ
        $user = User::factory()->create();
        $document = $this->makeAndGetTestDocument(['version_number' => 1, 'creator_id' => $user->id]);

        // Обновляем документ
        $updatedDocument = DocumentService::createNewVersion($document);

        // Проверяем, что новая версия документа сохранена в базе данных
        $this->assertDatabaseHas('documents_documents', ['id' => $updatedDocument->id, 'version_number' => 2]);
        $this->assertEquals(2, $updatedDocument->version_number);
        $this->assertNotEquals($document->id, $updatedDocument->id);
    }

    public function testValidateAccessToDocument(): void
    {
        // Создаем пользователя и документ
        $user = User::factory()->create();
        $document = $this->makeAndGetTestDocument(['creator_id' => $user->id]);

        // Проверяем права доступа
        $result = DocumentService::validateAccessToDocument($document, $user, AccessType::READ);

        $this->assertTrue($result);
    }
}
