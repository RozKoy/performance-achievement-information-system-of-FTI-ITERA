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
        Schema::create('rs_targets', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('target');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('indikator_kinerja_id')->constrained('indikator_kinerja');
            $table->foreignUuid('unit_id')->constrained('units');

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rs_targets');
    }
};
