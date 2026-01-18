<?php declare(strict_types=1);

namespace Modules\ParimZharim\Orders\Domain\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Shared\Payment\Domain\Models\Payment;

/**
 * Class OrderPayment
 */
class OrderPayment extends Payment
{

    protected $table = 'payment_payments';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'payable_order_id');
    }
}
