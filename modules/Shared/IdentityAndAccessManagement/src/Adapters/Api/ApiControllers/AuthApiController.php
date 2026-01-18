<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByEmail;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetUserProfileByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutCurrentDevice;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutDevice;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\LogoutAllDevices;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RegisterUserByPhoneIfNotExists;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestAuthenticationPhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\EmailAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Random\RandomException;

class AuthApiController extends BaseApiController {
    /**
     * @throws InvalidInputData
     * @throws RandomException
     */
    public function requestAuthPhoneCode(Request $request): JsonResponse {
        $request->validate([
            'phone' => 'required|string|regex:/^\+[0-9]{10,14}$/',
        ]);

        $phone = $request->input('phone');

        $userProfileData = new UserProfileData(phone: $phone, name: $phone);

        RegisterUserByPhoneIfNotExists::make()->handle($userProfileData);

        $user = GetUserProfileByPhone::make()->handle($phone);
        if (!$user) {
            return $this->respondError("Error while registering User", 500);
        }

        try {
            RequestAuthenticationPhoneVerificationCodeForUser::make()->handle($user, $phone);
        } catch (PhoneVerificationCodeRateLimitError $e) {
            return $this->respondError($e->getMessage(), 423);
        }

        return $this->respondSuccess();
    }

    public function getAccessTokenByAuthPhoneCode(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+[0-9]{10,14}$/',
            'code' => 'required|string|regex:/^[0-9]{6}$/',
            'device_id' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');
        $device_id = $request->input('device_id');

        $user = GetUserProfileByPhone::make()->handle($phone);
        if (!$user) {
            return $this->respondError("User not found", 404);
        }

        // Authenticate the user
        try {
            $token = AuthenticateUserByPhone::make()->handle(
                new PhoneAuthenticationRequestData(
                    phone: $phone,
                    code: (int) $code,
                    device_id: $device_id
                )
            );
        } catch (InvalidInputData $e) {
            return $this->respondError($e->getMessage(), 400);
        } catch (AuthenticationError $e) {
            return $this->respondError($e->getMessage(), 401);
        } catch (UserNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        return $this->respond(['token' => $token, 'user' => $user]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => 'required|string',
        ]);

        $device_id = $request->input('device_id');

        $user = $request->user();
        if (!$user) {
            return $this->respondError("User not found", 404);
        }

        LogoutDevice::make()->handle($user, $device_id);

        return $this->respondSuccess();
    }

    public function logoutCurrentDevice(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->respondError("User not found", 404);
        }
        LogoutCurrentDevice::make()->handle($user);

        return $this->respondSuccess();
    }

    public function logoutAllDevices(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return $this->respondError("User not found", 404);
        }

        LogoutAllDevices::make()->handle($user);

        return $this->respondSuccess();
    }

    /**
     * Authenticate user by email and password and return an access token.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAccessTokenByEmailPassword(Request $request): JsonResponse
    {
        // Validate request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Create an instance of EmailAuthenticationRequestData
        $emailAuthenticationRequestData = new EmailAuthenticationRequestData(
            email: $request->input('email'),
            password: $request->input('password')
        );

        // Attempt to authenticate the user
        try {
            $token = AuthenticateUserByEmail::make()->handle($emailAuthenticationRequestData);
        } catch (InvalidInputData $e) {
            return $this->respondError($e->getMessage(), 400);
        } catch (AuthenticationError $e) {
            return $this->respondError($e->getMessage(), 401);
        } catch (UserNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        // Respond with the token and user information
        $user = Auth::user();
        return $this->respond([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }
}
