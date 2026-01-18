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
        Schema::create('documents_documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number');
            $table->string('type');
            $table->string('status');
            $table->integer('version_number');
            $table->foreignId('creator_id');
            $table->foreignId('package_id')->nullable();
            $table->string('file')->nullable();
            $table->jsonb('content')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamp('date_from');
            $table->timestamp('date_to')->nullable();
            $table->string('access_mode');
            $table->string('default_access_type');
            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
            $table->index('creator_id');
            $table->index('package_id');
            $table->index('date_from');
            $table->index('date_to');
            $table->index(['type', 'number']);
            $table->unique(['type', 'number', 'version_number']);
        });

        $this->createGINIndex();
    }

    private function createGINIndex(): void
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
            try {
                DB::statement('CREATE INDEX documents_documents_name_gin_index ON documents_documents USING GIN (name gin_trgm_ops);');
                DB::statement('CREATE INDEX documents_documents_number_gin_index ON documents_documents USING GIN (number gin_trgm_ops);');
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
        Schema::dropIfExists('documents_documents');
    }
};
