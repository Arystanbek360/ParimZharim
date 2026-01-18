<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class AuthenticateUserByPhone extends BaseAction {

    public function __construct(
        private readonly UserRepository                  $userRepository,
    ) {}

    /**
     * @throws InvalidInputData
     * @throws UserNotFound
     * @throws AuthenticationError
     */
    public function handle(PhoneAuthenticationRequestData $data): string
    {
        // validate that data contains a valid phone number
        ValidateAuthenticationPhoneNumber::make()->handle($data->phone);

        // Find user by phone
        $user = $this->userRepository->findByPhone($data->phone);
        if (!$user) {
            throw new UserNotFound("User not found");
        }

        // Check if the code is correct
        VerifyPhoneVerificationCodeForUser::make()->handle($user, $data->phone, (string) $data->code);

        // Set phone verified if not set
        if ($user->phone_verified_at === null) {
            $user->phone_verified_at = now();
            $this->userRepository->save($user);
        }

        if ($data->device_id === 'WEB') {
            // Session auth
            Auth::login($user);
            return "WEB";
        } else {
            // Token Auth
            $token = $user->createToken($data->device_id);
            return $token->plainTextToken;
        }
    }

}
