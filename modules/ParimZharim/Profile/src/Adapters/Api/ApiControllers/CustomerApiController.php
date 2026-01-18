<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Profile\Adapters\Api\Transformers\CustomerTransformer;
use Modules\ParimZharim\Profile\Application\Actions\ChangePhoneNumberForUserAndCustomer;
use Modules\ParimZharim\Profile\Application\Actions\CreateCustomerForUserIfNotExists;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerById;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerByPhone;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerByUser;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\ParimZharim\Profile\Application\Actions\DeleteCustomer;
use Modules\ParimZharim\Profile\Application\Actions\UpdateCustomer;
use Modules\ParimZharim\Profile\Application\DTO\CustomerProfileData;
use Modules\ParimZharim\Profile\Domain\Errors\CustomerNotFound;
use Modules\ParimZharim\Profile\Application\ApplicationError\CannotDeleteCustomerProfile;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\AuthenticateUserByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\Actions\GetUserProfileByPhone;
use Modules\Shared\IdentityAndAccessManagement\Application\DTO\PhoneAuthenticationRequestData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\AuthenticationError;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\InvalidInputData;
use Modules\Shared\IdentityAndAccessManagement\Application\Errors\UserNotFound;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Throwable;

class CustomerApiController extends BaseApiController {

    public function __construct()
    {
        $this->setTransformer();
    }

    /**
     * @throws InvalidInputData
     * @throws InvalidDataTransformer
     * @throws Throwable
     */
    public function register(Request $request): JsonResponse {
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'required|date_format:Y-m-d',
        ]);

        $name = $request->input('name');
        $birthDate = $request->input('birth_date');

        $age = Carbon::parse($birthDate)->age;
        if ($age < 18) {
            return $this->respondError('User must be at least 18 years old.', 422);
        }

        /** @var User $user */
        $user = $request->user();
        $phone = $user->phone;

        if (!$phone) {
            throw new InvalidInputData("User phone number is not set.");
        }

        // Find existing Customer by phone
        $customer = GetCustomerByPhone::make()->handle($phone);

        // If Customer not found, create a new one
        if (!$customer) {
            $customerProfile = new CustomerProfileData(
                name: $name,
                phone: $phone,
                email: null,
                dateOfBirth: $birthDate
            );

            CreateCustomerForUserIfNotExists::make()->handle($customerProfile, $user);
            $customer = GetCustomerByPhone::make()->handle($phone);
        }

        if ($user->id !== $customer->user_id) {
            throw new InvalidInputData("User with the same phone number is not bound to this customer.");
        }

        return $this->respondWithTransformer($customer);
    }


    /**
     * @throws InvalidDataTransformer
     */
    public function getProfile(Request $request): JsonResponse
    {
        $user = $request->user();
        $customer = GetCustomerByUser::make()->handle($user);
        if (!$customer) {
            return $this->respondNotFound('Customer not found.');
        }

        return $this->respondWithTransformer($customer);
    }

    /**
     * @throws CustomerNotFound
     * @throws Throwable
     */
    public function update(Request $request): JsonResponse
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'birth_date' => 'nullable|date_format:Y-m-d',
        ]);

        $name = $request->input('name');
        $birthDate = $request->input('birth_date');

        $user = $request->user();
        $customer = GetCustomerByUser::make()->handle($user);
        if (!$customer) {
            return $this->respondNotFound('Customer not found.');
        }

        // Check if provided birth date matches the current birth date
        if ($birthDate && $customer->date_of_birth && $birthDate !== $customer->date_of_birth->format('Y-m-d')) {
            return $this->respondError('Changing birth date is not allowed.', 422);
        }

        // Prepare Customer Profile Data
        $customerProfileData = new CustomerProfileData(
            name: $name,
            dateOfBirth: $birthDate,
        );

        // Update customer profile
        UpdateCustomer::make()->handle($customer, $customerProfileData);

        // refresh data
        $customer = GetCustomerById::make()->handle($customer->id);
        return $this->respondWithTransformer($customer);
    }


    public function delete(Request $request): JsonResponse
    {
        // Find Customer by authenticated User
        $user = $request->user();
        $customer = GetCustomerByUser::make()->handle($user);
        if (!$customer) {
            return $this->respondNotFound('Customer not found.');
        }
        try {
            DeleteCustomer::make()->handle($customer->id);
        } catch (CustomerNotFound $e) {
            return $this->respondNotFound($e->getMessage());
        } catch (CannotDeleteCustomerProfile $e) {
            return $this->respondInternalError($e->getMessage());
        }

        return $this->respondSuccess('Customer profile deleted successfully.');
    }

    /**
     * @throws Throwable
     */
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

        // Find User Profile (from IDM module) and error if not found
        $user = GetUserProfileByPhone::make()->handle($phone);
        if (!$user) {
            return $this->respondError("User not found", 404);
        }

        return $this->authenticateCustomerAndRespondWithToken($user, $phone, $code, $device_id);
    }

    private function authenticateCustomerAndRespondWithToken(User $user, $phone, $code, $deviceId): JsonResponse
    {
        try {
            $token = AuthenticateUserByPhone::make()->handle(
                new PhoneAuthenticationRequestData(
                    phone: $phone,
                    code: (int) $code,
                    device_id: $deviceId,
                )
            );
        } catch (InvalidInputData $e) {
            return $this->respondError($e->getMessage(), 400);
        } catch (AuthenticationError $e) {
            return $this->respondError($e->getMessage(), 401);
        } catch (UserNotFound $e) {
            return $this->respondError($e->getMessage(), 404);
        }

        $customer = GetCustomerByPhone::make()->handle($phone);
        $customerResponse = $customer ? $this->transformer->transform($customer) : null;

        return $this->respond(['token' => $token, 'customer' => $customerResponse]);
    }

    /**
     * @throws InvalidInputData
     * @throws AuthenticationError
     * @throws Throwable
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

        ChangePhoneNumberForUserAndCustomer::make()->handle($user, $phone, (int) $code);

        return $this->respondSuccess();
    }

    private function setTransformer(): void
    {
        $this->transformer = new CustomerTransformer();
    }
}
