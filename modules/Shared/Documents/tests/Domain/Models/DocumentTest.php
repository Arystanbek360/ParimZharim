<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Tests\Domain\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\Tag;
use Modules\Shared\Documents\Tests\TestCase;
use Modules\Shared\IdentityAndAccessManagement\Database\Factories\UserFactory;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

class DocumentTest extends TestCase
{


    protected const string TABLE = 'documents_documents';
    protected const string USERS_TABLE = 'idm_users';
    protected Document $testDocument;
    protected array $testAttributes = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->testAttributes = [
            'name' => "Test docУмент",
            'number' => "TEST-ДОК_123",
            'type' => 'its type',
            'status' => 'its status',
            'creator_id' => UserFactory::new()->create()->id,
            'package_id' => null,
            'file' => null,
            'content' => ['con' => "tent"],
            'metadata' => ['meta' => "data"],
            'date_from' => Carbon::now()->copy(),
            'date_to' => null,
            'access_mode' => AccessMode::SPECIFIC_USERS,
            'default_access_type' => AccessType::READ
        ];

        $this->testDocument = Document::makeDocumentInstance($this->testAttributes);
        $this->testDocument->save();
    }

    /**
     * Проверка: создается валидный экземпляр модели с соответсвующими обязательными атрибутами.
     * @return void
     */
    public function testCreateModel()
    {
        $model = $this->testDocument;
        $attr = $this->testAttributes;

        $this->assertNotNull($model);
        $this->assertSame(1, $model->version_number);
        $this->assertSame($attr['name'], $model->name);
        $this->assertSame($attr['number'], $model->number);
        $this->assertSame($attr['type'], $model->type);
        $this->assertSame($attr['status'], $model->status);
        $this->assertSame($attr['creator_id'], $model->creator_id);
        $this->assertSame($attr['package_id'], $model->package_id);
        $this->assertSame($attr['file'], $model->file);
        $this->assertSame($attr['content'], $model->content);
        $this->assertSame($attr['metadata'], $model->metadata);
//         $this->assertEquals($attr['date_from'], $model->date_from->format('Y-m-d H:i:s'));
//        $this->assertEquals($attr['date_to'], $model->date_to->format('Y-m-d H:i:s'));
        $this->assertSame($attr['access_mode'], $model->access_mode);
        $this->assertSame($attr['default_access_type'], $model->default_access_type);
    }

    /**
     * Проверка: Модель сохраняется в базе данных.
     * @return void
     */
    public function testDatabaseHasInstance(): void
    {
        $testName = $this->testAttributes['name'];
        $testNumber = $this->testAttributes['number'];
        $testType = $this->testAttributes['type'];
        $testStatus = $this->testAttributes['status'];
        $testCreatorId = $this->testAttributes['creator_id'];
        $testContent = $this->testAttributes['content'];
        $testMetadata = $this->testAttributes['metadata'];
        $testDataFrom = $this->testAttributes['date_from'];
        $testAccessMode = $this->testAttributes['access_mode'];
        $testAccessType = $this->testAttributes['default_access_type'];


        $this->assertDatabaseHas(self::USERS_TABLE, ['id' => $testCreatorId]);
        $this->assertDatabaseHas(self::TABLE,
            [
                'id' => $this->testDocument->id,
                'name' => $testName,
                'number' => $testNumber,
                'type' => $testType,
                'status' => $testStatus,
                'version_number' => 1,
                'creator_id' => $testCreatorId,
                'content' => json_encode($testContent),
                'metadata' => json_encode($testMetadata),
                'date_from' => $testDataFrom,
                'access_mode' => $testAccessMode->value,
                'default_access_type' => $testAccessType->value,
            ]);
    }

    /**
     * Проверка: Теги корректно связываются с документом.
     * @return void
     */
    public function testTagsRelationship()
    {
        $document = $this->testDocument;

        $tagToAttach = Tag::factory()->create();
        $document->tags()->attach($tagToAttach->id);

        $attachedTag = $document->tags()->find($tagToAttach->id);
        $this->assertNotNull($attachedTag);
        $this->assertSame(Tag::class, get_class($attachedTag));
        /** @var Tag $attachedTag */
        $this->assertSame($tagToAttach->id, $attachedTag->id);

        $document->tags()->detach($attachedTag->id);
        $this->assertNull($document->tags()->find($attachedTag->id));

        $count = 5;
        $tags = Tag::factory($count)->create();
        /** @var Tag $tag */
        foreach ($tags as $tag) {
            $document->tags()->attach($tag->id);
        }

        $attachedTags = $document->tags()->get();
        $this->assertSame($count, $attachedTags->count());

        for ($i = 0; $i < $count; $i++) {
            $this->assertSame($tags[$i]->id, $attachedTags[$i]->id);
            $this->assertSame($tags[$i]->name, $attachedTags[$i]->name);
        }
    }

    /**
     * Проверка: Пользователи корректно связываются с документом.
     * @return void
     */
    public function testUsersRelationship()
    {
        $document = $this->testDocument;

        $userToAttach = User::factory()->create();
        $document->users()->attach($userToAttach->id);

        $attachedUser = $document->users()->find($userToAttach->id);
        $this->assertNotNull($attachedUser);
        $this->assertSame(User::class, get_class($attachedUser));
        /** @var User $attachedUser */
        $this->assertSame($userToAttach->id, $attachedUser->id);
        $this->assertSame($userToAttach->name, $attachedUser->name);

        $document->users()->detach($attachedUser->id);
        $this->assertNull($document->users()->find($attachedUser->id));

        $count = 5;
        $users = User::factory($count)->create();
        /** @var User $user */
        foreach ($users as $user) {
            $document->users()->attach($user->id);
        }

        $attachedUsers = $document->users()->get();
        $this->assertSame($count, $attachedUsers->count());

        /** @var array<User> $users */
        /** @var array<User> $attachedUsers */
        for ($i = 0; $i < $count; $i++) {
            $this->assertSame($users[$i]->id, $attachedUsers[$i]->id);
            $this->assertSame($users[$i]->name, $attachedUsers[$i]->name);
            $this->assertSame($users[$i]->name, $attachedUsers[$i]->name);
            $this->assertSame($document->default_access_type, $attachedUsers[$i]->pivot->access_type);
        }
    }
}
