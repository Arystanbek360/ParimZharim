<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

interface UserRepository extends BaseRepositoryInterface {

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public function findById(string $id): ?User;

    public function save(User $user): void;
}
