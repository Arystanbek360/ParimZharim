<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Api\ApiControllers\Webhooks;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\Shared\Core\Adapters\Api\BaseApiController;
use Modules\Shared\Payment\Application\Actions\QueryPaymentByID;
use Modules\Shared\Payment\Application\Actions\SyncPaymentStatusFromPaymentSystem;
use Modules\Shared\Payment\Domain\Models\Payment;

class TipTopPayWebhookController extends BaseApiController
{
    /**
     * Handles incoming CloudPayments webhook notifications.
     *
     * @param Request $request
     * @param string $type Type of the webhook ('Check', 'Confirm', 'Pay', 'Fail')
     * @return JsonResponse
     */
    public function handleWebhook(Request $request, string $type): JsonResponse
    {
        if (!$this->isValidHmac($request)) {
            return response()->json(['code' => '-1', 'message' => 'Invalid HMAC'], 401);
        }

        $data = $request->all();
        $invoiceID = (int) $data['InvoiceId'] ?? null;

        if (!$invoiceID) {
            return response()->json(['code' => '-1', 'message' => 'Missing InvoiceId'], 400);
        }

        $payment = QueryPaymentByID::make()->handle($invoiceID);
        if (!$payment) {
            return response()->json(['code' => '-1', 'message' => 'Payment not found'], 404);
        }

        if (!$this->canSyncPaymentData($data, $payment)) {
            return response()->json(['code' => '-1', 'message' =>'Invalid payment data'], 400);
        }

        SyncPaymentStatusFromPaymentSystem::make()->handle($payment);

        // Дополнительная логика в зависимости от типа уведомления может быть добавлена здесь, если нужно.
        return response()->json(['code' => '0']);
    }

    /**
     * Validate HMAC headers to ensure the integrity and authenticity of the incoming webhook notifications.
     *
     * @param Request $request
     * @return bool
     */
    private function isValidHmac(Request $request): bool
    {
        $apiSecret = config('app.payment_tiptoppay_secret');
        $computedHmac = base64_encode(hash_hmac('sha256', $request->getContent(), $apiSecret, true));

        return $computedHmac === $request->header('Content-HMAC') || $computedHmac === $request->header('X-Content-HMAC');
    }

    private function canSyncPaymentData(array $data, Payment $payment): bool
    {
        return $data['Amount'] == $payment->total
            && (int) $data['InvoiceId'] == $payment->id
            && (int) $data['AccountId'] == $payment->customer_id;
    }
}
