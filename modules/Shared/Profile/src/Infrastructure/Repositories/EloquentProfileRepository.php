<?php declare(strict_types=1);

namespace Modules\Shared\Profile\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Errors\UserWithEmailAlreadyExists;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Profile\Domain\Errors\CannotCreateUserForThisProfile;
use Modules\Shared\Profile\Domain\Models\Profile;
use Modules\Shared\Profile\Domain\Repositories\ProfileRepository;
use Throwable;

class EloquentProfileRepository extends BaseRepository implements ProfileRepository
{

    /**
     * Создается пользователь для профиля если он еще не создан
     * @param Profile $profile
     * @throws CannotCreateUserForThisProfile
     */
    public function createAssociatedUserForProfileIfNotExists(Profile $profile): void
    {
        $associatedUser = $profile->user;
        if (!$associatedUser) {
            try {
                DB::beginTransaction();
                $user = User::create([
                    'name' => $profile->name,
                    'email' => $profile->email ?? null,
                    'phone' => $profile->phone ?? null,
                    'password' => Str::password(64)
                ]);
                $profile->user()->associate($user);
                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                throw new CannotCreateUserForThisProfile();
            }
        }
    }

    /**
     * Обновляет данные пользователя для профиля, если они были изменены
     * @param Profile $profile
     * @throws UserWithEmailAlreadyExists
     */
    public function updateAssociatedUserForProfile(Profile $profile): void
    {
        $user = $profile->user;

        if ($profile->email) {
            $existingUser = User::where('email', $profile->email)->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                throw new UserWithEmailAlreadyExists($profile->email);
            }
        }
        if ($profile->phone) {
            $existingUser = User::where('phone', $profile->phone)->first();
            if ($existingUser && $existingUser->id !== $user->id) {
                throw new UserWithEmailAlreadyExists($profile->phone);
            }
        }

        if ($user) {
            $user->name = $profile->name;
            $user->email = $profile->email;
            $user->phone = $profile->phone;
            $user->save();
        }
    }


    /**
     * Создает пользователя для профиля, если он еще не создан и обновляет данные пользователя для профиля
     * @param Profile $profile
     * @throws CannotCreateUserForThisProfile
     * @throws UserWithEmailAlreadyExists
     */
    public function createUserForProfileIfNotExistsAndUpdateUser(Profile $profile): void
    {
        try {
            DB::beginTransaction();
            $this->createAssociatedUserForProfileIfNotExists($profile);
            $this->updateAssociatedUserForProfile($profile);
            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Удаляет профиль и пользователя связанного с ним
     * @param Profile $profile
     */
    public function deleteProfileWithUser(Profile $profile): void
    {
        if ($profile->user) {
            $profile->user->delete();
        }
    }

    /**
     * Восстанавливает профиль и пользователя связанного с ним
     * @param Profile $profile
     */
    public function restoreProfileWithUser(Profile $profile): void
    {
        $user = $profile->user()->withTrashed()->first();
        if ($user) {
            $user->restore();
        }
    }

    public function getProfileById(int $profileId): ?Profile
    {
        return Profile::find($profileId);
    }

    public function getProfileByPhone(string $phone): ?Profile
    {
        return Profile::where('phone', $phone)->first();
    }

    public function getProfileByUser(User $user): ?Profile
    {
        return Profile::where('user_id', $user->id)->first();
    }

    public function getProfileByEmail(string $email): ?Profile
    {
        return Profile::where('email', $email)->first();
    }

    public function saveProfile(Profile $profile): void
    {
        $profile->save();
    }

    public function saveProfileAndAssociateWithUser(Profile $profile, User $user): void
    {
        $profile->user_id = $user->id;
        $profile->save();
    }
}
