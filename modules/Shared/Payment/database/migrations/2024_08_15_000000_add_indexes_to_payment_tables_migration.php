<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_payment_items', function (Blueprint $table) {
            $table->index('payment_id');
        });

        Schema::table('payment_payments', function (Blueprint $table) {
            $table->index('payable_order_id');
            $table->index('customer_id');
            $table->index('payment_method');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_payment_items', function (Blueprint $table) {
            $table->dropIndex(['payment_id']);
        });

        Schema::table('payment_payments', function (Blueprint $table) {
            $table->dropIndex(['payable_order_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['payment_method']);
            $table->dropIndex(['status']);
        });
    }
};
