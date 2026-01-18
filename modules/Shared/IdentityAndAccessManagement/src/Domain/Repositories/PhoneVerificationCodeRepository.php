<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Domain\Repositories;

use Modules\Shared\Core\Domain\BaseRepositoryInterface;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;

interface PhoneVerificationCodeRepository extends BaseRepositoryInterface {

    public function create(User $user, string $phone, int $code): void;

    public function deleteAllForUser(User $user): void;

    public function deleteOlderThan(int $days = 60): void;

    public function deleteForPeriod(string $dayFrom, string $dayTo): void;

    public function findLastAndActiveForUserAndPhone(User $user, string $phone): ?PhoneVerificationCode;

    public function findLastByPhone(string $phone): ?PhoneVerificationCode;

    public function countAllByPhonePerDay(string $phone): int;

    public function countAllPerDay(): int;

    public function markAsExpired(PhoneVerificationCode $phoneVerificationCode): void;

    public function markAllOldCodesForPhoneAsExpired(string $phone): void;
}
