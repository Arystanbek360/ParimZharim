<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\NovaComponents\ReservationsTool\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectsCategories;
use Modules\ParimZharim\Orders\Application\Actions\GetOrderableServiceObjectsForReservationTable;
use Modules\ParimZharim\Orders\Domain\Models\Order;
use Modules\Shared\Core\Adapters\Api\BaseApiController;

class ReservationsController extends BaseApiController
{
    public function index(Request $request): JsonResponse
    {
        if (!Auth::user()->can('viewAny', Order::class)) {
            return $this->respondError('Unauthorized', 403);
        }

        $categories = $request->get('categories', []);
        $date = $request->get('date');

        return $this->respond(GetOrderableServiceObjectsForReservationTable::make()->handle($categories, $date));
    }

    public function objectCategories(Request $request): JsonResponse
    {
        if (!Auth::user()->can('viewAny', Order::class)) {
            return $this->respondError('Unauthorized', 403);
        }
        return $this->respond(GetOrderableServiceObjectsCategories::make()->handle());
    }
}
