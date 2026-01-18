<?php declare(strict_types=1);

namespace Modules\ParimZharim\LoyaltyProgram\Database\Migrations;

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
        Schema::create('loyalty_program_discount_tiers', function (Blueprint $table) {
            $table->id();
            $table->integer('discount_percentage');
            $table->decimal('threshold_amount', 15, 2);
            $table->timestamp('start_date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyalty_program_discount_tiers');
    }
};
