<?php declare(strict_types=1);

namespace ParimZharim\Objects\Database\Migrations;

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
     * Create B-tree indexes for various tables.
     */
    private function createIndexes(): void
    {
        Schema::table('objects_service_objects', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('objects_categories', function (Blueprint $table) {
            $table->index('is_visible_to_customers');
        });

        Schema::table('objects_tags', function (Blueprint $table) {
            $table->index('is_visible_to_customers');
        });

        Schema::table('objects_object_to_tag', function (Blueprint $table) {
            $table->index('service_object_id');
            $table->index('tag_id');
        });
    }

    /**
     * Create GIN indexes using raw SQL for PostgreSQL.
     */
    private function createGINIndexes(): void
    {
        try {
            DB::statement('CREATE INDEX objects_service_objects_name_gin_index ON objects_service_objects USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX objects_categories_name_gin_index ON objects_categories USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX objects_tags_name_gin_index ON objects_tags USING GIN (name gin_trgm_ops);');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Drop indexes during the rollback of the migration.
     */
    private function dropIndexes(): void
    {
        Schema::table('objects_service_objects', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('objects_categories', function (Blueprint $table) {
            $table->dropIndex(['is_visible_to_customers']);
        });

        Schema::table('objects_tags', function (Blueprint $table) {
            $table->dropIndex(['is_visible_to_customers']);
        });

        Schema::table('objects_object_to_tag', function (Blueprint $table) {
            $table->dropIndex(['service_object_id']);
            $table->dropIndex(['tag_id']);
        });
    }

    /**
     * Drop GIN indexes using raw SQL for PostgreSQL during the rollback.
     */
    private function dropGINIndexes(): void
    {
        try {
            DB::statement('DROP INDEX IF EXISTS objects_service_objects_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS objects_categories_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS objects_tags_name_gin_index;');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }
};
