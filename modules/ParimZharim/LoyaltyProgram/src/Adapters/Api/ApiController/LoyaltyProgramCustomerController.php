<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Adapters\Api\ApiController;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ParimZharim\LoyaltyProgram\Application\Actions\GetCurrentAndNextCustomerDiscount;
use Modules\ParimZharim\LoyaltyProgram\Domain\Errors\LoyaltyProgramCustomerNotFound;
use Modules\ParimZharim\Profile\Application\Actions\GetCustomerByUser;
use Modules\Shared\Core\Adapters\Api\BaseApiController;

class LoyaltyProgramCustomerController extends BaseApiController
{

    /**
     * @throws LoyaltyProgramCustomerNotFound
     */
    public function getCurrentAndNextCustomerDiscount(Request $request): JsonResponse
    {
        $user = $request->user();
        $customer = GetCustomerByUser::make()->handle($user);
        $discount = GetCurrentAndNextCustomerDiscount::make()->handle($customer->id);
        return $this->respond($discount);
    }
}
