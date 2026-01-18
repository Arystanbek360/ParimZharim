<?php declare(strict_types=1);

namespace Modules\Orders\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Throwable;

return new class extends Migration {
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Support for multiple schemas
            DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
            $this->createGINIndexes();
        }

        $this->createIndexes();
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            $this->dropGINIndexes();
        }
        $this->dropIndexes();
    }


    private function createIndexes(): void
    {
        Schema::table('orders_orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('start_time');
        });

        Schema::table('orders_order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('orderable_id');
            $table->index('type');
        });

        Schema::table('orders_plans', function (Blueprint $table) {
            $table->index('plan_type');
        });

        Schema::table('orders_schedule_to_object', function (Blueprint $table) {
            $table->index('schedule_id');
            $table->index('orderable_service_object_id');
        });

        Schema::table('orders_plan_to_object', function (Blueprint $table) {
            $table->index('plan_id');
            $table->index('orderable_service_object_id');
        });
    }

    private function createGINIndexes(): void
    {
        try {
            DB::statement('CREATE INDEX orders_schedules_name_gin_index ON orders_schedules USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX orders_plans_name_gin_index ON orders_plans USING GIN (name gin_trgm_ops);');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }

    private function dropIndexes(): void
    {
        Schema::table('orders_orders', function (Blueprint $table) {
            $table->dropIndex(['status']);  // Use the column name in an array
            $table->dropIndex(['start_time']);
        });

        Schema::table('orders_order_items', function (Blueprint $table) {
            $table->dropIndex(['order_id']);
            $table->dropIndex(['orderable_id']);
            $table->dropIndex(['type']);
        });

        Schema::table('orders_plans', function (Blueprint $table) {
            $table->dropIndex(['plan_type']);
        });

        Schema::table('orders_schedule_to_object', function (Blueprint $table) {
            $table->dropIndex(['schedule_id']);
            $table->dropIndex(['orderable_service_object_id']);
        });

        Schema::table('orders_plan_to_object', function (Blueprint $table) {
            $table->dropIndex(['plan_id']);
            $table->dropIndex(['orderable_service_object_id']);
        });
    }

    private function dropGINIndexes(): void
    {
        try {
            DB::statement('DROP INDEX IF EXISTS orders_schedules_name_gin_index;');
            DB::statement('DROP INDEX IF EXISTS orders_plans_name_gin_index;');
        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }
};
