<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Models\Profile;

interface ProfileRepository extends BaseRepositoryInterface {

    public function createAssociatedUserForProfileIfNotExists(Profile $profile): void;

    public function updateAssociatedUserForProfile(Profile $profile): void;

    public function createUserForProfileIfNotExistsAndUpdateUser(Profile $profile): void;

    public function deleteProfileWithUser(Profile $profile): void;

    public function restoreProfileWithUser(Profile $profile): void;

    public function getProfileById(int $profileId): ?Profile;

    public function getProfileByPhone(string $phone): ?Profile;

    public function getProfileByUser(User $user): ?Profile;

    public function getProfileByEmail(string $email): ?Profile;

    public function saveProfile(Profile $profile): void;

    public function saveProfileAndAssociateWithUser(Profile $profile, User $user): void;
}
