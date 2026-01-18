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
        //add editor_id column
        Schema::table('documents_documents', function (Blueprint $table) {
            $table->foreignId('editor_id')->nullable()->after('creator_id');
            $table->index('editor_id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents_documents', function (Blueprint $table) {
            $table->dropColumn('editor_id');
        });
    }
};
