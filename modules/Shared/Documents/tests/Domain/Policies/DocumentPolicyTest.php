<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Policies;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Shared\Documents\Database\Factories\DocumentFactory;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Policies\DocumentPolicy;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;


class DocumentPolicyTest extends TestCase
{


    protected Document $publicDocumentToRead;
    protected Document $publicDocumentToWrite;
    protected Document $protectedDocument;

    protected User $creator;
    protected User $specificUserCanRead;
    protected User $specificUserCanWrite;
    protected User $anyUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->publicDocumentToRead = $this->makeAndGetTestDocument();
        $this->publicDocumentToWrite = $this->makeAndGetTestDocument();
        $this->protectedDocument = $this->makeAndGetTestDocument();

        $this->creator = User::factory()->create();
        $this->specificUserCanRead = User::factory()->create();
        $this->specificUserCanWrite = User::factory()->create();
        $this->anyUser = User::factory()->create();

        $this->publicDocumentToRead->access_mode = AccessMode::ANY_USER;
        $this->publicDocumentToWrite->access_mode = AccessMode::ANY_USER;
        $this->protectedDocument->access_mode = AccessMode::SPECIFIC_USERS;
        $this->publicDocumentToRead->default_access_type = AccessType::READ;
        $this->publicDocumentToWrite->default_access_type = AccessType::WRITE;

        $this->publicDocumentToRead->creator_id = $this->creator->id;
        $this->publicDocumentToWrite->creator_id = $this->creator->id;
        $this->protectedDocument->creator_id = $this->creator->id;

        $this->protectedDocument->users()->attach($this->specificUserCanRead->id);
        $this->protectedDocument->users()->updateExistingPivot($this->specificUserCanRead->id, ['access_type' => AccessType::READ]);
        $this->protectedDocument->users()->attach($this->specificUserCanWrite->id);
        $this->protectedDocument->users()->updateExistingPivot($this->specificUserCanWrite->id, ['access_type' => AccessType::WRITE]);
    }

    public function testViewAny(): void
    {
        $policy = new DocumentPolicy();

        $this->assertTrue($policy->viewAny($this->creator));
        $this->assertTrue($policy->viewAny($this->specificUserCanRead));
        $this->assertTrue($policy->viewAny($this->specificUserCanWrite));
        $this->assertTrue($policy->viewAny($this->anyUser));
    }

    public function testView(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->view($this->creator, $document));
        $this->assertTrue($policy->view($this->specificUserCanRead, $document));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->view($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->view($this->creator, $document));
        $this->assertTrue($policy->view($this->specificUserCanRead, $document));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->view($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->view($this->creator, $document));
        $this->assertTrue($policy->view($this->specificUserCanRead, $document));
        $this->assertTrue($policy->view($this->specificUserCanWrite, $document));
        $this->assertNull($policy->view($this->anyUser, $document));
    }

    public function testCreate(): void
    {
        $policy = new DocumentPolicy();

        $this->assertTrue($policy->create($this->creator));
        $this->assertTrue($policy->create($this->specificUserCanRead));
        $this->assertTrue($policy->create($this->specificUserCanWrite));
        $this->assertTrue($policy->create($this->anyUser));
    }

    public function testUpdate(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->update($this->creator, $document));
        $this->assertNull($policy->update($this->specificUserCanRead, $document));
        $this->assertNull($policy->update($this->specificUserCanWrite, $document));
        $this->assertNull($policy->update($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->update($this->creator, $document));
        $this->assertTrue($policy->update($this->specificUserCanRead, $document));
        $this->assertTrue($policy->update($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->update($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->update($this->creator, $document));
        $this->assertNull($policy->update($this->specificUserCanRead, $document));
        $this->assertTrue($policy->update($this->specificUserCanWrite, $document));
        $this->assertNull($policy->update($this->anyUser, $document));
    }

    public function testReplicate(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }

    public function testDelete(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }

    public function testForceDelete(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertFalse($policy->forceDelete($this->creator, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->forceDelete($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertFalse($policy->forceDelete($this->creator, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->forceDelete($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertFalse($policy->forceDelete($this->creator, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanRead, $document));
        $this->assertFalse($policy->forceDelete($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->forceDelete($this->anyUser, $document));
    }

    public function testRestore(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }

    public function testAttachTag(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->attachTag($this->creator, $document));
        $this->assertNull($policy->attachTag($this->specificUserCanRead, $document));
        $this->assertNull($policy->attachTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->attachTag($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->attachTag($this->creator, $document));
        $this->assertTrue($policy->attachTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->attachTag($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->attachTag($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->attachTag($this->creator, $document));
        $this->assertNull($policy->attachTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->attachTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->attachTag($this->anyUser, $document));
    }

    public function testAnyAttachTag(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->attachAnyTag($this->creator, $document));
        $this->assertNull($policy->attachAnyTag($this->specificUserCanRead, $document));
        $this->assertNull($policy->attachAnyTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->attachAnyTag($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->attachAnyTag($this->creator, $document));
        $this->assertTrue($policy->attachAnyTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->attachAnyTag($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->attachAnyTag($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->attachAnyTag($this->creator, $document));
        $this->assertNull($policy->attachAnyTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->attachAnyTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->attachAnyTag($this->anyUser, $document));
    }

    public function testDetachTag(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->detachTag($this->creator, $document));
        $this->assertNull($policy->detachTag($this->specificUserCanRead, $document));
        $this->assertNull($policy->detachTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->detachTag($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->detachTag($this->creator, $document));
        $this->assertTrue($policy->detachTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->detachTag($this->specificUserCanWrite, $document));
        $this->assertTrue($policy->detachTag($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->detachTag($this->creator, $document));
        $this->assertNull($policy->detachTag($this->specificUserCanRead, $document));
        $this->assertTrue($policy->detachTag($this->specificUserCanWrite, $document));
        $this->assertNull($policy->detachTag($this->anyUser, $document));
    }

    public function testAttachUser(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }

    public function testAnyAttachUser(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }

    public function testDetachUser(): void
    {
        $policy = new DocumentPolicy();

        $document = $this->publicDocumentToRead;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->publicDocumentToWrite;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));

        $document = $this->protectedDocument;

        $this->assertTrue($policy->restore($this->creator, $document));
        $this->assertFalse($policy->restore($this->specificUserCanRead, $document));
        $this->assertFalse($policy->restore($this->specificUserCanWrite, $document));
        $this->assertFalse($policy->restore($this->anyUser, $document));
    }
}
