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
        Schema::create('template_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('display_text')->nullable();
            $table->text('country')->nullable();
            $table->text('email')->nullable();
            $table->text('photo')->nullable();
            $table->text('biography')->nullable();
            $table->integer('number');
            $table->string('type');
            $table->string('password');
            $table->string('slug')->unique()->nullable();
            $table->string('url')->nullable()->default('https://example.com/');
            $table->timestamp('date');
            $table->decimal('price', 10, 2);
            $table->boolean('active')->default(false);
            $table->jsonb('options')->nullable();
            $table->jsonb('template_data')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_templates');
    }
};
