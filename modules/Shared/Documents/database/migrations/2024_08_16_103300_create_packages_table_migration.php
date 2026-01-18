<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->string('status');
            $table->foreignId('creator_id');
            $table->foreignId('parent_package_id')->nullable();
            $table->jsonb('metadata');
            $table->string('access_mode');
            $table->string('default_access_type');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
            $table->index('creator_id');
            $table->index('parent_package_id');
        });

        $this->createGINIndex();
    }

    private function createGINIndex(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
            try {
                DB::statement('CREATE INDEX documents_packages_name_gin_index ON documents_packages USING GIN (name gin_trgm_ops);');
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
        Schema::dropIfExists('documents_packages');
    }
};
