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
        //add total_with_discount to orders_order_items
        Schema::table('orders_order_items', function (Blueprint $table) {
            $table->decimal('total_with_discount', 10, 2)->default(0)->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //drop total_with_discount from orders_order_items
        Schema::table('orders_order_items', function (Blueprint $table) {
            $table->dropColumn('total_with_discount');
        });

    }
};
