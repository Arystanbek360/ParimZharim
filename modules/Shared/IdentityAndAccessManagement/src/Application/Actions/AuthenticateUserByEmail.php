<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Application\Actions;

use Illuminate\Support\Facades\Auth;
use Modules\Shared\Core\Application\BaseAction;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\EmailAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Domain\Repositories\UserRepository;

class AuthenticateUserByEmail extends BaseAction {

    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    /**
     * @throws InvalidInputData
     * @throws UserNotFound
     * @throws AuthenticationError
     */
    public function handle(EmailAuthenticationRequestData $data): string {
        // Validate that data contains a valid email
        if (!filter_var($data->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidInputData("Invalid email address");
        }

        // Authenticate user by email and password
        $user = $this->userRepository->findByEmail($data->email);
        if (!$user) {
            throw new UserNotFound("User not found");
        }

        if (!Auth::attempt(['email' => $data->email, 'password' => $data->password])) {
            throw new AuthenticationError("Invalid email or password");
        }

        Auth::login($user);
        $token = $user->createToken('user-token');
        return $token->plainTextToken;
    }
}
