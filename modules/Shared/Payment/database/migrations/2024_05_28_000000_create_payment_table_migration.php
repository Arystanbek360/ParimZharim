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
        Schema::create('payment_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payable_order_id');
            $table->unsignedBigInteger('customer_id');
            $table->string('status');
            $table->decimal('total', 10, 2);
            $table->string('payment_method');
            $table->jsonb('metadata')->nullable();
            $table->string('external_id')->unique()->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payments');
    }
};
