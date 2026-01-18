<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Infrastructure\Repositories;

use Illuminate\Support\Carbon;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\PhoneVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PhoneVerificationCodeRepository;

class EloquentPhoneVerificationCodeRepository extends BaseRepository implements PhoneVerificationCodeRepository {
    private const int CODE_DURATION = 30;

    public function create(User $user, string $phone, int $code): void
    {
        $verificationCode = new PhoneVerificationCode();
        $verificationCode->user_id = $user->id;
        $verificationCode->phone = $phone;
        $verificationCode->code = $code;
        $verificationCode->expires_at = now()->addMinutes(self::CODE_DURATION);
        $verificationCode->save();
    }

    public function deleteForPeriod(string $dayFrom, string $dayTo): void
    {
        if ($dayFrom === $dayTo) {
            $dayTo = Carbon::parse($dayTo)->endOfDay();
        }
        PhoneVerificationCode::whereBetween('created_at', [$dayFrom, $dayTo])->delete();
    }


    public function deleteAllForUser(User $user): void
    {
        PhoneVerificationCode::where('user_id', $user->id)->delete();
    }

    public function deleteOlderThan(int $days = 60): void
    {
        PhoneVerificationCode::where('created_at', '<', now()->subDays($days))->delete();
    }

    public function findLastAndActiveForUserAndPhone(User $user, string $phone): ?PhoneVerificationCode
    {
        return PhoneVerificationCode::where('phone', $phone)
            ->where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function findLastByPhone(string $phone): ?PhoneVerificationCode
    {
        return PhoneVerificationCode::where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    public function countAllByPhonePerDay(string $phone): int
    {
        return PhoneVerificationCode::where('phone', $phone)
            ->where('created_at', '>', now()->subDay())
            ->count();
    }

    public function countAllPerDay(): int
    {
        return PhoneVerificationCode::where('created_at', '>', now()->subDay())
            ->count();
    }

    public function markAsExpired(PhoneVerificationCode $phoneVerificationCode): void
    {
        $phoneVerificationCode->expires_at = now();
        $phoneVerificationCode->save();
    }

    public function markAllOldCodesForPhoneAsExpired(string $phone): void
    {
        $codes = PhoneVerificationCode::where('phone', $phone)
            ->where('expires_at', '>', now())
            ->get();
        if ($codes->isEmpty()) {
            return;
        }
        foreach ($codes as $code) {
            $this->markAsExpired($code);
        }
    }

}
