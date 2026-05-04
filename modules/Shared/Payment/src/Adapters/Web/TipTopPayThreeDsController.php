<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Adapters\Web;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Modules\Shared\Payment\Application\Actions\CompletePayment;
use Modules\Shared\Payment\Application\Actions\QueryPaymentByID;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Infrastructure\Services\TipTopPayPaymentService;

class TipTopPayThreeDsController
{
    public function showChallenge(int $paymentID): View
    {
        $payment = QueryPaymentByID::make()->handle($paymentID);
        if (!$payment) {
            abort(404);
        }

        $threeDs = $payment->metadata['threeDs'] ?? null;
        if (!$threeDs || empty($threeDs['acsUrl']) || empty($threeDs['paReq']) || empty($threeDs['transactionId'])) {
            abort(404);
        }

        return view('payment::components.tiptoppay-3ds-form', [
            'acsUrl' => $threeDs['acsUrl'],
            'paReq' => $threeDs['paReq'],
            'transactionId' => $threeDs['transactionId'],
            'termUrl' => route('payment.tiptoppay.post3ds', ['paymentID' => $payment->id], true),
        ]);
    }

    public function handleCallback(Request $request, int $paymentID): View
    {
        $payment = QueryPaymentByID::make()->handle($paymentID);
        if (!$payment) {
            abort(404);
        }

        $threeDs = $payment->metadata['threeDs'] ?? [];
        $transactionId = (string)$request->input('MD', '');
        $paRes = (string)$request->input('PaRes', '');

        if (!$paRes || !$transactionId || $transactionId !== (string)($threeDs['transactionId'] ?? '')) {
            return view('payment::components.tiptoppay-3ds-result', [
                'success' => false,
                'message' => 'Не удалось подтвердить 3-D Secure. Попробуйте оплатить другой картой.',
            ]);
        }

        $payment = app(TipTopPayPaymentService::class)->completeThreeDsPayment($payment, $transactionId, $paRes);

        if ($payment->status === PaymentStatus::SUCCESS) {
            CompletePayment::make()->handle($payment);
            $payment->refresh();
        }

        return view('payment::components.tiptoppay-3ds-result', [
            'success' => in_array($payment->status, [PaymentStatus::SUCCESS, PaymentStatus::COMPLETED], true),
            'message' => in_array($payment->status, [PaymentStatus::SUCCESS, PaymentStatus::COMPLETED], true)
                ? 'Оплата прошла успешно.'
                : 'Не удалось завершить оплату. Попробуйте оплатить другой картой.',
        ]);
    }
}
