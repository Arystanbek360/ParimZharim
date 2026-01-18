<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Document;
use Modules\Shared\Documents\Domain\Services\DocumentService;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Политика доступа `DocumentPolicy`
 * Предоставляет методы проверки прав доступа к модели `Document`.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class DocumentPolicy extends BasePolicy
{
    /**
     * Проверка прав доступа на просмотр списка документов.
     * Доступ есть у всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function viewAny(User $user): ?bool
    {
        return true;
    }

    /**
     * Проверка прав доступа на просмотр документа.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::READ`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function view(User $user, Document $document): ?bool
    {
        if (DocumentService::validateAccessToDocument($document, $user, AccessType::READ)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на создание документа.
     * Доступ есть у всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function create(User $user): ?bool
    {
        return true;
    }

    /**
     * Проверка прав доступа на обновление документа.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function update(User $user, Document $document): ?bool
    {
        if (DocumentService::validateAccessToDocument($document, $user, AccessType::WRITE)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа для дублирования документа.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function replicate(User $user, Document $document): ?bool
    {
        return $document->creator_id === $user->id;
    }

    /**
     * Проверка прав доступа на удаление документа.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function delete(User $user, Document $document): ?bool
    {
        return $document->creator_id === $user->id;
    }

    /**
     * Проверка прав доступа на жёсткое удаление документа.
     * Доступа запрещен для всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function forceDelete(User $user, Document $document): ?bool
    {
        return false;
    }

    /**
     * Проверка прав доступа на восстановление удалённого документа.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function restore(User $user, Document $document): ?bool
    {
        return $document->creator_id === $user->id;
    }

    /**
     * Проверка прав доступа на прикрепление тега к документу.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachTag(User $user, Document $document): ?bool
    {
        if (DocumentService::validateAccessToDocument($document, $user, AccessType::WRITE)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на прикрепление тега из списка тегов к документу.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachAnyTag(User $user, Document $document): ?bool
    {
        if (DocumentService::validateAccessToDocument($document, $user, AccessType::WRITE)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа для открепления тега от документа.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function detachTag(User $user, Document $document): ?bool
    {
        if (DocumentService::validateAccessToDocument($document, $user, AccessType::WRITE)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на прикрепление пользователя к документу.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachUser(User $user, Document $document): ?bool
    {
        if ($document->creator_id === $user->id) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на прикрепление пользователя из списка пользователей к документу.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachAnyUser(User $user, Document $document): ?bool
    {
        if ($document->creator_id === $user->id) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на открепления пользователя от документа.
     * Доступ есть только у создатея документа.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Document $document Документ - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function detachUser(User $user, Document $document): ?bool
    {
        if ($document->creator_id === $user->id) {
            return true;
        }

        return null;
    }
}
