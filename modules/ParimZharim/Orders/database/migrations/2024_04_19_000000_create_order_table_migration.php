<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orderable_service_object_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('creator_id');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->timestamp('confirm_before');
            $table->jsonb('metadata')->nullable();
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->index('orderable_service_object_id');
            $table->index('customer_id');
            $table->index('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_orders');
    }
};
