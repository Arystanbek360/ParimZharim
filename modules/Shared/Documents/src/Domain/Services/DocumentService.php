<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Services;

use Illuminate\Support\Facades\Log;
use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Repositories\DocumentRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

/**
 * Сервис `DocumentService`
 * Предоставляет централизованное управление всеми операциями, связанными с документами.
 * Сервисы отвечают за логику создания, обновления, получения и архивации документов, а также за управление доступом.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class DocumentService extends BaseService
{
    /**
     * Получить связанный репозиторий документов.
     * @return DocumentRepository
     */
    protected static function getDocumentRepository(): DocumentRepository
    {
        return app(DocumentRepository::class);
    }

    /**
     * Получить связанный репозиторий пользователей.
     * @return UserRepository
     */
    protected static function getUserRepository(): UserRepository
    {
        return app(UserRepository::class);
    }

    /**
     * Сохранить документ в базе данных.
     * @param Document $document
     * @return void
     */
    public static function saveDocument(Document $document): void
    {
        self::getDocumentRepository()->saveDocument($document);
    }

    public static function saveDocumentQuietly(Document $document): void
    {
        self::getDocumentRepository()->saveDocumentQuietly($document);
    }

    /**
     * Обновить и сохранить тип доступ по-умолчанию для документа.
     * @param Document $document
     * @param AccessType $accessType
     * @return void
     */
    public static function updateDefaultAccessType(Document $document, AccessType $accessType): void
    {
        $document->default_access_type = $accessType;
        self::saveDocument($document);
    }

    /**
     * Обновить и сохранить режим доступа для документа.
     * @param Document $document
     * @param AccessMode $accessMode
     * @return void
     */
    public static function updateAccessMode(Document $document, AccessMode $accessMode): void
    {
        $document->access_mode = $accessMode;
        self::saveDocument($document);
    }

    /**
     * Создание нового документа версии 1.
     * @param Document $document Модель документа, которую нужно сохранить в БД как новый документ.
     * @param User|null $creator Пользователь-создатель документа.
     * @return Document Возвращает экземпляр сохраненного документа.
     * @throws ValidationError Выбрасывает исключение, если документ не прошел валидацию и не может быть сохранен.
     */
    public static function createNewDocument(Document $document, ?User $creator = null): Document
    {
        if ($creator) {
            $document->creator_id = $creator->id;
        }
        self::validateDocument($document, true);
        self::saveDocument($document);
        return $document;
    }

    /**
     * Обновление и сохранение в БД документа с созданием новой версии.
     * @param Document $document Обновленный документ, который нужно сохранить как новую версию.
     * @return Document Возвращает объект обновленного документа.
     * @throws ValidationError Выбрасывает исключение, если версия документа невалидна и не может быть сохранена.
     */
    public static function createNewVersion(Document $document): Document
    {
        self::validateDocument($document);
        // Клонируем документ и увеличиваем номер версии
        $newVersion = $document->replicate();
        Log::info('New version id = ', ['id' => $newVersion->id]);
        $maxVersionNumber = self::getDocumentRepository()->getMaxVersionNumber($document);
        if ($maxVersionNumber === null) {
            $maxVersionNumber = 0;
        }
        $newVersion->version_number = $maxVersionNumber + 1;
        $newVersion->created_at = now();
       // $newVersion->id = null;
        $newVersion->exists = false;// Устанавливаем время создания новой версии

        // Сохраняем новую версию документа
       self::saveDocumentQuietly($newVersion);

        return $newVersion;
    }

    /**
     * Проверка запрашиваемых прав доступа пользователя к документу.
     * @param Document $document Документ, к которому нужно проверить доступ.
     * @param User $user Пользователь, доступ которого нужно проверить.
     * @param AccessType $reqAccessType Запрашиваемый доступ.
     * @return bool Вернет `true`, если у пользователя есть запрашиваемый доступ к документу, `false` - нет доступа.
     */
    public static function validateAccessToDocument(Document $document, User $user, AccessType $reqAccessType): bool
    {
        // если пользователь - создатель документа
        if ($document->creator_id === $user->id) return true;

        // получение количественного значения уровней доступа
        $reqAccessLevel = $reqAccessType->getLevel();
        $docAccessLevel = $document->default_access_type->getLevel();

        // если документ доступен для всех пользователей
        if ($document->access_mode === AccessMode::ANY_USER) {
            return $reqAccessLevel <= $docAccessLevel;
        }

        // если документ только для конкретных пользователей
        $specificUser = $document->users()->find($user->id);
        // если нужный пользователь найден среди привязанных к документу пользователей
        if ($specificUser) {
            $userAccess = $specificUser->pivot->access_type;
            return $reqAccessLevel <= $userAccess->getLevel();
        }
        return false;
    }

    /**
     * Проверяет документ на валидность:
     * - `date_to` позже `date_from`;
     * - связанные создатель документа и пакет существуют в базе данных;
     * - если это новый документ, то значение `version_number` = 1.
     * @param Document $document
     * @param bool $isNew Проверка документа с учётом того, что это новый документ.
     * @throws ValidationError Выбрасывает ошибку, если документ не прошел валидацию.
     */
    public static function validateDocument(Document $document, bool $isNew = false): void
    {
        if ($isNew) {
            if ($document->version_number !== 1)
                throw new ValidationError("Невалидный Документ: версия должна быть 1");
        }

        if ($document->date_to) {
            if ($document->date_from->greaterThan($document->date_to))
                throw new ValidationError("Невалидный документ: $document->date_from не может быть позже $document->date_to");
        }

        $creatorId = (string)$document->creator_id;
        if (!self::getUserRepository()->findById($creatorId)) {
            throw new ValidationError("Невалидный Документ: создатель документа не найден");
        }

        if ($document->package_id) {
            if (!QueryService::getPackageById($document->package_id)) {
                throw new ValidationError("Невалидный Документ: Пакет для этого документа не найден");
            }
        }
    }
}
