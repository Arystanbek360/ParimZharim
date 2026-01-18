<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\PersonalAccessTokenRepository;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserDeviceRepository;
use Throwable;

class LogoutAllDevices extends BaseAction {

    public function __construct(
        private readonly PersonalAccessTokenRepository $personalAccessTokenRepository,
        private readonly UserDeviceRepository $userDeviceRepository
    ) {}

    /**
     * @throws Throwable
     */
    public function handle(User $user): void {
        DB::beginTransaction();
        try {
        $this->personalAccessTokenRepository->deleteAllUserTokens($user);
        $this->userDeviceRepository->deleteAllUserDevices($user);
        DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

    }
}
