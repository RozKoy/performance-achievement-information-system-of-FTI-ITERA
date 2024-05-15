<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rs_years', function (Blueprint $table) {
            $table->uuid('id');

            $table->char('year', 4);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->unique('year');
        });

        Schema::table('sasaran_strategis', function (Blueprint $table) {
            $table->foreignUuid('time_id')->constrained('rs_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rs_years');
    }
};
