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
        Schema::create('products_and_services_service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_visible_to_customers')->default(true);
            $table->timestamps();
            $table->softDeletes();  // Adds a 'deleted_at' column for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_and_services_service_categories');
    }
};
