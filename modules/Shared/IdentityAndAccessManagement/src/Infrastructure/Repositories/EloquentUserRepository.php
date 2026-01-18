<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories;

use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class EloquentUserRepository extends BaseRepository implements UserRepository {

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return User::where('phone', $phone)->first();
    }

    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function save(User $user): void
    {
        $user->save();
    }
}
