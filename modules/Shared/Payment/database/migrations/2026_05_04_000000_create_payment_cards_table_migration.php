<?php declare(strict_types=1);

namespace Modules\Shared\Payment\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('token')->unique();
            $table->string('card_mask')->nullable();
            $table->string('card_first_six', 6)->nullable();
            $table->string('card_last_four', 4)->nullable();
            $table->unsignedTinyInteger('expiration_date_month')->nullable();
            $table->unsignedSmallInteger('expiration_date_year')->nullable();
            $table->string('card_exp_date', 5)->nullable();
            $table->string('card_type')->nullable();
            $table->string('issuer')->nullable();
            $table->string('gateway_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_cards');
    }
};
