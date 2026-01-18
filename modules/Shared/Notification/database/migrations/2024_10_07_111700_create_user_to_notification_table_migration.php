<?php declare(strict_types=1);

namespace Modules\Shared\Notification\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Запуск миграции.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('notifications_user_to_notification', function (Blueprint $table) {
            $table->foreignId('user_id')->unsigned();
            $table->foreignId('notification_id')->unsigned();
            $table->string('status');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('user_id');
            $table->index('notification_id');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    /**
     * Откат миграции.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_user_to_notification');
    }
};
