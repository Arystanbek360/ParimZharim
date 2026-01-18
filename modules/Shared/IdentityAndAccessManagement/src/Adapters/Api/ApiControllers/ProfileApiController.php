<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\ChangePhoneForUserByVerificationCode;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\RequestPhoneChangePhoneVerificationCodeForUser;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\UpdateUserProfile;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneChangeRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\UserProfileData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\PhoneVerificationCodeRateLimitError;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Random\RandomException;

class ProfileApiController extends BaseApiController {
    /**
     * @throws InvalidInputData
     * @throws RandomException
     */
    public function requestPhoneChangePhoneCode(Request $request): JsonResponse {
        $request->validate([
            'phone' => 'required|string|regex:/^\+[0-9]{10,14}$/',
        ]);

        $phone = $request->input('phone');

        /** @var User $user */
        $user = auth()->user();

        try {
            RequestPhoneChangePhoneVerificationCodeForUser::make()->handle($user, $phone);
        } catch (PhoneVerificationCodeRateLimitError $e) {
            return $this->respondError($e->getMessage(), 423);
        }

        return $this->respondSuccess();
    }

    /**
     * @throws InvalidInputData
     * @throws AuthenticationError
     */
    public function changePhone(Request $request): JsonResponse {
        $request->validate([
            'phone' => 'required|string|regex:/^\+[0-9]{10,14}$/',
            'code' => 'required|string|regex:/^[0-9]{6}$/',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');

        /** @var User $user */
        $user = auth()->user();

        ChangePhoneForUserByVerificationCode::make()->handle(new PhoneChangeRequestData($user, $phone, (int) $code));

        return $this->respondSuccess();
    }

    public function getProfile(Request $request): JsonResponse {
        $user = auth()->user();
        return $this->respond($user);
    }

    public function updateProfile(Request $request): JsonResponse {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $name = $request->input('name');

        /** @var User $user */
        $user = auth()->user();

        UpdateUserProfile::make()->handle($user, new UserProfileData(name: $name));

        $user->refresh();

        return $this->respond($user);
    }
}
