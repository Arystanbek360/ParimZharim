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
        Schema::create('cms_content_categories', function (Blueprint $table) {
            $table->id(); // Primary key: id
            $table->string('name'); // Название категории
            $table->text('description')->nullable(); // Описание категории (nullable)
            $table->jsonb('metadata')->nullable(); // Метаданные категории (nullable)
            $table->timestamps();
            $table->softDeletes(); // Soft delete
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_content_categories');
    }
};
