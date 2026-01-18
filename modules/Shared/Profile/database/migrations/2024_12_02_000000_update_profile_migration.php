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
        Schema::table('profile_profiles', function (Blueprint $table) {
            // Удаляем старый индекс, если он есть
            $table->dropIndex(['user_id']);

            // Добавляем уникальное ограничение для user_id
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_profiles', function (Blueprint $table) {
            $table->dropUnique(['user_id']);
            $table->index('user_id'); // Восстанавливаем старый индекс
        });
    }
};
