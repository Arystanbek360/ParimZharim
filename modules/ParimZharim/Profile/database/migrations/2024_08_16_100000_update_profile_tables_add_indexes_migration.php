<?php declare(strict_types=1);

namespace Modules\ParimZharim\Profile\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;

return new class extends Migration {

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Support for multiple schemas
            DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
            $this->createGINIndexes();
        }
        $this->createIndexes();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->dropGINIndexes();
        }
        $this->dropIndexes();
    }

    /**
     * Create GIN indexes for phone, email, and name fields using the pg_trgm extension.
     */
    private function createGINIndexes(): void
    {
        try {
            DB::statement('CREATE INDEX profile_customers_phone_gin_index ON profile_customers USING GIN (phone gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_customers_email_gin_index ON profile_customers USING GIN (email gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_customers_name_gin_index ON profile_customers USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_employees_name_gin_index ON profile_employees USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_employees_email_gin_index ON profile_employees USING GIN (email gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_employees_phone_gin_index ON profile_employees USING GIN (phone gin_trgm_ops);');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Create B-tree indexes for other fields.
     */
    private function createIndexes(): void
    {
        Schema::table('profile_customers', function (Blueprint $table) {
            $table->index('date_of_birth');
        });
    }

    /**
     * Drop GIN and B-tree indexes during the rollback of the migration.
     */
    private function dropGINIndexes(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            try {
                DB::statement('DROP INDEX IF EXISTS profile_customers_phone_gin_index;');
                DB::statement('DROP INDEX IF EXISTS profile_customers_email_gin_index;');
                DB::statement('DROP INDEX IF EXISTS profile_customers_name_gin_index;');
                DB::statement('DROP INDEX IF EXISTS profile_employees_name_gin_index;');
            } catch (Throwable $e) {
                report($e);
                throw $e;
            }
        }
    }

    private function dropIndexes(): void
    {
        Schema::table('profile_customers', function (Blueprint $table) {
            $table->dropIndex(['date_of_birth']);
        });
    }
};
