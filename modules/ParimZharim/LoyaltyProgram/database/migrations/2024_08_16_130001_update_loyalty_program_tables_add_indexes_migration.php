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
        Schema::table('loyalty_program_coupons', function (Blueprint $table) {
            $table->index('creator_id');
            $table->index('type');
            $table->index('valid_from');
            $table->index('valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Removing indexes from 'loyalty_program_coupons'
        Schema::table('loyalty_program_coupons', function (Blueprint $table) {
            $table->dropIndex(['creator_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['valid_from']);
            $table->dropIndex(['valid_until']);
        });
    }
};
