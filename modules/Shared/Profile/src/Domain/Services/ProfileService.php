<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Services;

use Modules\Shared\Core\Domain\BaseDomainService;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;

class ProfileService extends BaseDomainService
{
    public static function getProfileRepository(): ProfileRepository
    {
        return app(ProfileRepository::class);
    }

    public static function createUserIfNotExistsAndUpdateUser(Profile $profile): void
    {
        $repository = self::getProfileRepository();
        $repository->createUserForProfileIfNotExistsAndUpdateUser($profile);
    }

    public static function deleteProfileWithUser(Profile $profile): void
    {
        $repository = self::getProfileRepository();
        $repository->deleteProfileWithUser($profile);
    }

    public static function restoreProfileWithUser(Profile $profile): void
    {
        $repository = self::getProfileRepository();
        $repository->restoreProfileWithUser($profile);
    }

    public static function getProfileByUser(User $user): ?Profile
    {
        $repository = self::getProfileRepository();
        return $repository->getProfileByUser($user);
    }
}
