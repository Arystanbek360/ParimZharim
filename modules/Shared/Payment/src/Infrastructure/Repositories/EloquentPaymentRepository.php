<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Modules\Shared\Core\Infrastructure\BaseRepository;
use Modules\Shared\Payment\Domain\Models\Payment;
use Modules\Shared\Payment\Domain\Models\PaymentCollection;
use Modules\Shared\Payment\Domain\Models\PaymentItemCollection;
use Modules\Shared\Payment\Domain\Models\PaymentStatus;
use Modules\Shared\Payment\Domain\Repositories\PaymentRepository;
use Modules\Shared\Payment\Infrastructure\Errors\PaymentItemWasNotSaved;
use Modules\Shared\Payment\Infrastructure\Errors\PaymentWasNotSaved;
use Modules\Shared\Payment\Domain\Models\PaymentMethodType;
use Throwable;

class EloquentPaymentRepository extends BaseRepository implements PaymentRepository {
    public function getPaymentById(int $id): Payment
    {
        return Payment::find($id);
    }

    public function getPaymentsForOrder(int $orderId): PaymentCollection
    {
        $payments = Payment::where('payable_order_id', $orderId)->get();
        return new PaymentCollection($payments);
    }

    public function getPaymentsByStatus(PaymentStatus $status): PaymentCollection
    {
        $payments = Payment::where('status', $status->value)->get();
        return new PaymentCollection($payments);
    }

    /**
     * @throws PaymentWasNotSaved
     * @throws Throwable
     * @throws PaymentItemWasNotSaved
     */
    public function savePayment(Payment $payment, ?PaymentItemCollection $paymentItems = null): void
    {
        try {
            DB::beginTransaction();

            // Ensure the payment is saved first to generate an ID.
            if (!$payment->save()) {
                throw new PaymentWasNotSaved();
            }

            // Check if paymentItems is not null before iterating
            if ($paymentItems !== null) {
                foreach ($paymentItems as $paymentItem) {
                    $paymentItem->payment_id = $payment->id;
                    if (!$paymentItem->save()) {
                        throw new PaymentItemWasNotSaved();
                    }
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


    public function getPaymentStatusForPayment(Payment $payment): PaymentStatus
    {
        return $payment->status;
    }

    public function getAllPayments(): PaymentCollection
    {
        $payments = Payment::all();
        return new PaymentCollection($payments);
    }

    public function findPaymentsByOrderAndStatus(int $orderId, array $statuses, ?PaymentMethodType $paymentMethodType = null): ?PaymentCollection
    {
        $query = Payment::where('payable_order_id', $orderId)->whereIn('status', $statuses);
        if ($paymentMethodType !== null) {
            $query->where('payment_method', $paymentMethodType->value);
        }
        $payments = $query->get();
        return new PaymentCollection($payments);
    }
}
