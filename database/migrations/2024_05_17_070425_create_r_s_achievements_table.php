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

            $table->string('realization');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('indikator_kinerja_id')->constrained('indikator_kinerja');
            $table->foreignUuid('period_id')->constrained('rs_periods');
            $table->foreignUuid('unit_id')->constrained('units');
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
