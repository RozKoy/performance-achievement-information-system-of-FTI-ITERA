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
        Schema::create('rs_achievements', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('realization')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('indikator_kinerja_id')->constrained('indikator_kinerja');
            $table->foreignUuid('unit_id')->constrained('units');

            $table->unique('indikator_kinerja_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rs_achievements');
    }
};
