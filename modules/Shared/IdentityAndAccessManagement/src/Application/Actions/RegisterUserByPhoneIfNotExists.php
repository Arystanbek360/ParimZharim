<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Str;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class RegisterUserByPhoneIfNotExists extends BaseAction {

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * @throws InvalidInputData
     */
    public function handle(UserProfileData $data): void {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($data->phone);

        // Register user if not exists
        if (!$this->userRepository->findByPhone($data->phone)) {
            $user = new User();
            $user->phone = $data->phone;
            $user->name = $data->name ?? null;
            $user->email = $data->email ?? null;
            $user->password = $data->password ?? Str::password(64);
            $this->userRepository->save($user);
        }
    }

}
