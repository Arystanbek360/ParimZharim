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
        // update profile_customers table max 100
        Schema::table('profile_customers', function (Blueprint $table) {
            $table->integer('discount')->default(0)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_customers', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
    }
};
