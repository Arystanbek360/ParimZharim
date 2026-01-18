<?php declare(strict_types=1);

namespace Modules\ProductsAndServices\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;

return new class extends Migration {
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
     * Create B-tree indexes for visibility and activity fields.
     */
    private function createIndexes(): void
    {
        Schema::table('products_and_services_product_categories', function (Blueprint $table) {
            $table->index('is_visible_to_customers');
        });

        Schema::table('products_and_services_products', function (Blueprint $table) {
            $table->index('is_active');
        });

        Schema::table('products_and_services_service_categories', function (Blueprint $table) {
            $table->index('is_visible_to_customers');
        });

        Schema::table('products_and_services_services', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    /**
     * Create GIN indexes using raw SQL for PostgreSQL for `name` fields.
     */
    private function createGINIndexes(): void
    {
        try {
            DB::statement('CREATE INDEX products_and_services_product_categories_name_gin_index ON products_and_services_product_categories USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX products_and_services_products_name_gin_index ON products_and_services_products USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX products_and_services_service_categories_name_gin_index ON products_and_services_service_categories USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX products_and_services_services_name_gin_index ON products_and_services_services USING GIN (name gin_trgm_ops);');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    /**
     * Drop B-tree indexes during the rollback of the migration.
     */
    private function dropIndexes(): void
    {
        Schema::table('products_and_services_product_categories', function (Blueprint $table) {
            $table->dropIndex(['is_visible_to_customers']);
        });

        Schema::table('products_and_services_products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('products_and_services_service_categories', function (Blueprint $table) {
            $table->dropIndex(['is_visible_to_customers']);
        });

        Schema::table('products_and_services_services', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }

    /**
     * Drop GIN indexes using raw SQL for PostgreSQL during the rollback.
     */
    private function dropGINIndexes(): void
    {
        try {
            DB::statement('DROP INDEX IF EXISTS products_and_services_product_categories_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS products_and_services_products_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS products_and_services_service_categories_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS products_and_services_services_name_gin_index;');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }
};
