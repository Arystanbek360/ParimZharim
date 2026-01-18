<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            try {
                DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
                DB::statement('CREATE INDEX idm_users_name_gin_index ON idm_users USING GIN (name gin_trgm_ops);');
                DB::statement('CREATE INDEX idm_users_email_gin_index ON idm_users USING GIN (email gin_trgm_ops);');
                DB::statement('CREATE INDEX idm_users_phone_gin_index ON idm_users USING GIN (phone gin_trgm_ops);');
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
                DB::statement('DROP INDEX IF EXISTS idm_users_name_gin_index;');
                DB::statement('DROP INDEX IF EXISTS idm_users_email_gin_index;');
                DB::statement('DROP INDEX IF EXISTS idm_users_phone_gin_index;');
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        }
    }
};
