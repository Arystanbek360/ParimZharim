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
        Schema::create('documents_package_to_user', function (Blueprint $table) {
            $table->foreignId('package_id');
            $table->foreignId('user_id');
            $table->string('access_type');
            $table->timestamps();

            $table->index('package_id');
            $table->index('user_id');
            $table->unique(['package_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents_package_to_user');
    }
};
