<?php declare(strict_types=1);

namespace Arista\Profile\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Throwable;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            try {
                DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
                DB::statement('CREATE INDEX idm_phone_verification_codes_phone_gin_index ON idm_phone_verification_codes USING GIN (phone gin_trgm_ops);');
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            try {
                DB::statement('DROP INDEX IF EXISTS idm_phone_verification_codes_phone_gin_index;');
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        }
    }
};
