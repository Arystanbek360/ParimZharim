<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Adapters\Api\ApiControllers;

use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Shared\Core\Adapters\InvalidDataTransformer;
use Modules\Shared\IdentityAndAccessManagement\Domain\Models\User;
use Modules\Shared\Notification\Application\UpdateDeviceToken;
use Throwable;

class NotifiableUserDeviceController extends BaseApiController
{

    /**
     * Получить список уведомлений для текущего пользователя.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws InvalidDataTransformer
     */
    public function updateUserDevice(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        if (!$user) {
            return $this->respondError('User not found', 404);
        }

        // Валидация входных параметров
        $validated = $request->validate([
            'device_id' => 'required|string',
            'device_token' => 'required|string',
        ]);

        try {
            UpdateDeviceToken::make()->handle($user, $validated['device_id'], $validated['device_token']);
            return $this->respondSuccess('Device token updated');
        } catch (Throwable $e) {
            throw new InvalidDataTransformer($e->getMessage());
        }
    }


}
