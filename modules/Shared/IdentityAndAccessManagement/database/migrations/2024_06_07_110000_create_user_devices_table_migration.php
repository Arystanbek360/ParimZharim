<?php declare(strict_types=1);

namespace Modules\Shared\IdentityAndAccessManagement\Database\Migrations;

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
        Schema::create('idm_user_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('device_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->unique(['user_id', 'device_id']);
        });
    }

    /**
     * Откат миграции.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('idm_user_devices');
    }
};
