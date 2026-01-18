<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Api\ApiControllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\Payment\Adapters\Api\Transformers\PaymentMethodTransformer;
use Modules\Shared\Payment\Adapters\Api\Transformers\PaymentTransformer;
use Modules\Shared\Payment\Application\Actions\GetPaymentsForOrder;
use Modules\Shared\Payment\Application\Actions\GetPaymentsMethodsForMobile;

class PaymentApiController extends BaseApiController
{
    public function getPaymentsForOrder(Request $request): JsonResponse
    {
        //get order id
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        $orderId = (int)$request->input('order_id');

        $payments = GetPaymentsForOrder::make()->handle($orderId);
        $transformer = new PaymentTransformer();
        $payments = $payments->map(function ($payment) use ($transformer) {
            return $transformer->transform($payment);
        });

        //add transformers
        return response()->json($payments);
    }

    public function getPaymentsMethodsForMobile(Request $request): JsonResponse
    {
        $paymentMethods = GetPaymentsMethodsForMobile::make()->handle();
        $transformer = new PaymentMethodTransformer();

        $transformedMethods = $paymentMethods->map(function ($paymentMethod) use ($transformer) {
            return $transformer->transform($paymentMethod);
        });

        return response()->json($transformedMethods);
    }

    public function createPayment()
    {
        dd(1);
        return response()->json('hello');
    }
}
