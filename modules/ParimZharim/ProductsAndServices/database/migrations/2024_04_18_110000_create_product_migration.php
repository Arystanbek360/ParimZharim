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
        Schema::create('products_and_services_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 18, 2);
            $table->boolean('is_active')->default(true);
            $table->string('image')->nullable();
            $table->unsignedBigInteger('product_category_id');
            $table->timestamps();
            $table->softDeletes();

            // Index for faster lookups on foreign key
            $table->index('product_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products_and_services_products');
    }
};
