<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents_tag_to_document', function (Blueprint $table) {
            $table->foreignId('tag_id');
            $table->foreignId('document_id');
            $table->timestamps();

            $table->index('tag_id');
            $table->index('document_id');
            $table->unique(['tag_id', 'document_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_tag_to_document');
    }
};
