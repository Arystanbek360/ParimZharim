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
        Schema::create('notifications_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->jsonb('metadata')->nullable();
            $table->timestamp('planed_send_at');
            $table->string('type')->default('info');
            $table->jsonb('channels');
            $table->timestamps();
            $table->softDeletes();

            $table->index('planed_send_at');
        });

    }

    /**
     * Откат миграции.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications_notifications');
    }
};
