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
        Schema::table('idm_user_devices', function (Blueprint $table) {
            $table->string('device_token')->nullable(); // Add device_token column
        });
    }

    /**
     * Откат миграции.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('idm_user_devices', function (Blueprint $table) {
            $table->dropColumn('device_token');
        });
    }
};
