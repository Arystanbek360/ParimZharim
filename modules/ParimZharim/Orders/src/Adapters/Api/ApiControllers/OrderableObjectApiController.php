<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\ParimZharim\Objects\Adapters\Api\Transformers\TagTransformer;
use Modules\ParimZharim\Orders\Adapters\Api\Transformers\ServiceObjectListTransformer;
use Modules\ParimZharim\Orders\Adapters\Api\Transformers\ServiceObjectTransformer;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectByID;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectsByCategoryOnDate;
use Modules\ParimZharim\Orders\Application\Actions\GetPlanDetailsForObjectOnDate;
use Modules\ParimZharim\Orders\Application\Actions\GetScheduleDetailsForObjectOnDate;
use Modules\ParimZharim\Orders\Application\Actions\GetSlotsAndDurationForServiceObjectOnDate;
use Modules\ParimZharim\Orders\Application\Actions\QueryOrderableServicesByOrderableServiceObjectID;
use Modules\ParimZharim\Orders\Domain\Errors\OrderableObjectNotFound;
use Modules\ParimZharim\ProductsAndServices\Adapters\Api\Transformers\ServiceTransformer;
use Modules\Shared\Core\Adapters\Api\BaseApiController;

class OrderableObjectApiController extends BaseApiController
{
    public function getFreeSlotsForObjectAndDate(Request $request): JsonResponse
    {
        $date = $request->query('date', null) ? Carbon::parse($request->query('date')) : now();
        $serviceObjectID = (int)$request->query('object_id', null);
        if (!$serviceObjectID) {
            return $this->respondError('Не указан ID объекта', 400);
        }
        $date = $date->format('Y-m-d');

        $serviceObject = GetOrderableServiceObjectByID::make()->handle($serviceObjectID);
        if (!$serviceObject) {
            return $this->respondError((new OrderableObjectNotFound($serviceObjectID))->getMessage(), 404);
        }
        $timezone = $serviceObject->getObjectTimezone();
        //without shiftTimezone it will return 05:00, but with shiftTimezone it will return 21:00
        $startDate = Carbon::parse($date)->copy()->shiftTimezone($timezone)->startOfDay();
        $endDate = Carbon::parse($date)->copy()->shiftTimezone($timezone)->endOfDay()->addHours(6);

        $freeSlots = GetSlotsAndDurationForServiceObjectOnDate::make()->handle($serviceObjectID, $startDate, $endDate);

        return $this->respond($freeSlots);
    }

    public function getOrderableServiceObjectByID(Request $request): JsonResponse
    {
        $serviceObjectID = (int)$request->query('id', null);
        $date = $request->query('date', null) ? Carbon::parse($request->query('date')) : now();

        if (!$serviceObjectID) {
            return $this->respondError('Не указан ID объекта', 400);
        }

        $object = GetOrderableServiceObjectByID::make()->handle($serviceObjectID);
        if (!$object) {
            return $this->respondError((new OrderableObjectNotFound($serviceObjectID))->getMessage(), 404);
        }
        $services = QueryOrderableServicesByOrderableServiceObjectID::make()->handle($object->id);

        $planDetails = GetPlanDetailsForObjectOnDate::make()->handle($object, $date);
        $prices = $planDetails['prices'];
        $kitchenDeposit = $planDetails['kitchen_deposit'] ?? null;
        $guestLimits = $planDetails['guest_limits'];

        $scheduleDetails = GetScheduleDetailsForObjectOnDate::make()->handle($object, $date);

        $minDuration = $scheduleDetails['min_duration'];
        $confirmationWaitingDuration = $scheduleDetails['confirmation_waiting_duration'];

        // Добавление минимальной продолжительности к объекту
        $min_hours = $minDuration === PHP_INT_MAX ? null : $minDuration / 60; // Преобразование минут в часы
        $service_transformer = new ServiceTransformer();
        $services = $services->map(function ($service) use ($service_transformer) {
            return $service_transformer->transform($service);
        });
        $tag_transformer = new TagTransformer();
        $tags = $object->tags()->get()->map(function ($tag) use ($tag_transformer) {
            return $tag_transformer->transform($tag);
        });

        $dataForTransformation = [
            'serviceObject' => $object,
            'price' => $prices,
            'kitchen_deposit' => $kitchenDeposit,
            'guest_limits' => $guestLimits,
            'min_hours' => $min_hours,
            'confirmation_waiting_duration' => $confirmationWaitingDuration,
            'services' => $services,
            'tags' => $tags
        ];

        $transformer = new ServiceObjectTransformer();

        $transformedData = $transformer->transform($dataForTransformation);
        return $this->respond($transformedData);
    }

    public function getOrderableServiceObjectsByCategoryID(Request $request): JsonResponse
    {
        $categoryID = (int)$request->query('category_id', null);
        $date = $request->query('date', null) ? Carbon::parse($request->query('date')) : now();

        $objects = GetOrderableServiceObjectsByCategoryOnDate::make()->handle($categoryID, $date);
        $transformedData = [];

        foreach ($objects as $object) {
            $planDetails = GetPlanDetailsForObjectOnDate::make()->handle($object, $date);
            $prices = $planDetails['prices'] ?? [];
            $kitchenDeposit = $planDetails['kitchen_deposit'] ?? null;

            $scheduleDetails = GetScheduleDetailsForObjectOnDate::make()->handle($object, $date);

            $minDuration = $scheduleDetails['min_duration'];
            $confirmationWaitingDuration = $scheduleDetails['confirmation_waiting_duration'];

            // Добавление минимальной продолжительности к объекту
            $min_hours = $minDuration === PHP_INT_MAX ? null : $minDuration / 60; // Преобразование минут в часы
            $dataForTransformation = [
                'serviceObject' => $object,
                'price' => $prices,
                'kitchen_deposit' => $kitchenDeposit,
                'confirmation_waiting_duration' => $confirmationWaitingDuration,
                'min_hours' => $min_hours
            ];

            $transformer = new ServiceObjectListTransformer();

            $transformedData[] = $transformer->transform($dataForTransformation);
        }

        return $this->respond($transformedData);
    }


}
