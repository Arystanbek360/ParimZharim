<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Services;

use Modules\Shared\Core\Infrastructure\BaseService;
use Modules\Shared\Documents\Domain\Errors\ValidationError;
use Modules\Shared\Documents\Domain\Models\AccessMode;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Repositories\PackageRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Сервис `PackageService`
 * Предоставляет централизованное управление всеми операциями, связанными с пакетами документов.
 * Сервисы отвечают за логику создания, обновления, получения и архивации пакетами, а также за управление доступом.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class PackageService extends BaseService
{
    public static function getPackageRepository(): PackageRepository
    {
        return app(PackageRepository::class);
    }

    /**
     * Обновляет тип доступа к пакету по-умолчанию.
     * @param Package $package Пакет, которому нужно обновить тип доступа по-умолчанию.
     * @param AccessType $accessType Новое значение типа доступа по-умолчанию.
     */
    public static function updateDefaultAccessType(Package $package, AccessType $accessType): void
    {
        $package->default_access_type = $accessType;
        self::savePackage($package);
    }

    /**
     * Обновляет режим доступа для пакета.
     * @param Package $package Пакет, для которого нужно обновить режим доступа.
     * @param AccessMode $accessMode Новое значение для режима доступа.
     */
    public static function updateAccessMode(Package $package, AccessMode $accessMode): void
    {
        $package->access_mode = $accessMode;
        self::savePackage($package);
    }

    /**
     * Сохранение документа в пакете.
     * @param Document $document Документ для сохранения в пакет.
     * @param Package $package Пакет для сохранения документа.
     * @return void
     * @throws ValidationError
     */
    public static function saveDocumentInPackage(Document $document, Package $package): void
    {
        DocumentService::validateDocument($document);
        self::getPackageRepository()->saveDocumentInPackage($document, $package);
    }

    /**
     * Сохранение нового пакета документов в БД.
     * @param Package $package Пакет для сохранения.
     * @return void
     */
    public static function savePackage(Package $package): void
    {
        self::getPackageRepository()->savePackage($package);
    }

    /**
     * Проверка прав доступа пользователя к пакету документов.
     * @param Package $package Пакет документов для проверки доступов к нему.
     * @param User $user Пользователь, для которого нужно проверить доступ.
     * @param AccessType $reqAccessType
     * @return bool
     */
    public static function validateAccessToPackage(Package $package, User $user, AccessType $reqAccessType): bool
    {
        // если пользователь - создатель пакета
        if ($package->creator_id === $user->id) return true;

        $reqAccessLevel = $reqAccessType->getLevel(); //запрашиваемый доступ
        $docAccessLevel = $package->default_access_type->getLevel();

        // если пакет доступен для всех пользователь
        if ($package->access_mode === AccessMode::ANY_USER) {
            return $reqAccessLevel <= $docAccessLevel;
        }

        // если пакет только для конкретных пользователей
        $usersToPackage = $package->users()->find($user->id);

        if ($usersToPackage) {
            $userAccess = $usersToPackage->pivot->access_type;
            return $reqAccessLevel <= $userAccess->getLevel();
        }

        return false;
    }
}
