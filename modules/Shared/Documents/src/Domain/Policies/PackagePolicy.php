<?php declare(strict_types=1);

namespace Modules\Shared\Documents\Domain\Policies;

use Modules\Shared\Core\Domain\BasePolicy;
use Modules\Shared\Documents\Domain\Models\AccessType;
use Modules\Shared\Documents\Domain\Models\Package;
use Modules\Shared\Documents\Domain\Services\PackageService;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

/**
 * Политика доступа `PackagePolicy`
 * Предоставляет методы проверки прав доступа к модели `Package`.
 *
 * @version 1.0.0
 * @since 2024-08-21
 */
class PackagePolicy extends BasePolicy
{
    /**
     * Проверка прав доступа на просмотр списка пакетов.
     * Доступ есть у всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function viewAny(User $user): ?bool
    {
        return true;
    }

    /**
     * Проверка прав доступа на просмотр пакета.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::READ`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function view(User $user, Package $package): ?bool
    {
        if (PackageService::validateAccessToPackage($package, $user, AccessType::READ)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на создание пакета.
     * Доступ есть у всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function create(User $user): ?bool
    {
        return true;
    }

    /**
     * Проверка прав доступа на обновление пакета.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function update(User $user, Package $package): ?bool
    {
        if (PackageService::validateAccessToPackage($package, $user, AccessType::WRITE)) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на удаление пакета.
     * Доступ есть только у создателя пакета.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function delete(User $user, Package $package): ?bool
    {
        return $package->creator_id === $user->id;
    }

    /**
     * Проверка прав доступа на жёсткое удаление пакета.
     * Доступ запрещен для всех пользователей системы.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function forceDelete(User $user, Package $package): ?bool
    {
        return false;
    }

    /**
     * Проверка прав доступа на восстановление удалённого пакета.
     * Доступ есть только у создателя пакета.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function restore(User $user, Package $package): ?bool
    {
        return $package->creator_id === $user->id;
    }

    /**
     * Проверка прав доступа на прикрепление документа к пакету.
     * Доступ есть только у пользователей, прошедших валидацию прав доступа для `AccessType::WRITE`.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function addDocument(User $user, Package $package): ?bool
    {
        $packageAccess = PackageService::validateAccessToPackage($package, $user, AccessType::WRITE);

        if ($packageAccess) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на прикрепление пользователя к пакету.
     * Доступ есть только у создателя пакета.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachUser(User $user, Package $package): ?bool
    {
        if ($package->creator_id === $user->id) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на прикрепление пользователя из списка пользователей к пакету.
     * Доступ есть только у создателя пакета.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function attachAnyUser(User $user, Package $package): ?bool
    {
        if ($package->creator_id === $user->id) {
            return true;
        }

        return null;
    }

    /**
     * Проверка прав доступа на открепление пользователя от пакета.
     * Доступ есть только у создателя пакета.
     * @param User $user Пользователь, право доступа которого нужно проверить.
     * @param Package $package Пакет - модель политики прав доступа.
     * @return bool|null Возвращает `true` - доступ разрешен, `false` - запрещен, `null` - доступ для суперадмина.
     */
    public function detachUser(User $user, Package $package): ?bool
    {
        if ($package->creator_id === $user->id) {
            return true;
        }

        return null;
    }
}
