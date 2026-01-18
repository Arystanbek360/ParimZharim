<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('objects_object_to_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('service_object_id');
            $table->unsignedBigInteger('tag_id');
            $table->timestamps();

            $table->primary(['service_object_id', 'tag_id']); // Composite primary key
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('objects_object_to_tag');
    }
};
