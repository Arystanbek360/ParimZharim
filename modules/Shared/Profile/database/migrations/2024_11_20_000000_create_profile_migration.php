<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('type');
            $table->jsonb('metadata')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('user_id');
            $table->index('type');
        });

        if (DB::connection()->getDriverName() === 'pgsql') {
            // Support for multiple schemas
            DB::statement('set search_path = ' . config('database.connections.pgsql.search_path') . ',public;');
            $this->createGINIndexes();
        }

    }

    private function createGINIndexes(): void
    {
        try {
            DB::statement('CREATE INDEX profile_profiles_name_gin_index ON profile_profiles USING GIN (name gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_profiles_phone_gin_index ON profile_profiles USING GIN (phone gin_trgm_ops);');
            DB::statement('CREATE INDEX profile_profiles_email_gin_index ON profile_profiles USING GIN (email gin_trgm_ops);');

        } catch (Throwable $e) {
            report($e);
            throw $e;
        }
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_profiles');
    }
};
