<?php

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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('short_name', 10);
            $table->string('name');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->unique('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('unit_id')->nullable()->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('units');
    }
};
