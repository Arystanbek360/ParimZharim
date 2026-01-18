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
        Schema::create('documents_document_to_user', function (Blueprint $table) {
            $table->foreignId('document_id');
            $table->foreignId('user_id');
            $table->string('access_type');
            $table->timestamps();

            $table->index('document_id');
            $table->index('user_id');
            $table->unique(['document_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_document_to_user');
    }
};
