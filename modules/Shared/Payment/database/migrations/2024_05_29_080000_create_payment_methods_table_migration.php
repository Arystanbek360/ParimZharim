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
        Schema::create('payment_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('type')->unique();
            $table->boolean('is_available_for_mobile');
            $table->boolean('is_available_for_admin');
            $table->boolean('is_available_for_web');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_payment_methods');
    }
};
