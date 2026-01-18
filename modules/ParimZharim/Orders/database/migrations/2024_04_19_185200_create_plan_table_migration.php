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
        Schema::create('orders_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('plan_type'); // Assumes 'plan_type' is stored as a string. If enum, adjust accordingly.
            $table->jsonb('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_plans');
    }
};
