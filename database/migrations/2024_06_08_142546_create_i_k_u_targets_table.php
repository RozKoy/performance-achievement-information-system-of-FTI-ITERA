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
        Schema::create('iku_targets', function (Blueprint $table) {
            $table->uuid('id');

            $table->integer('target');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('indikator_kinerja_program_id')->constrained('indikator_kinerja_program');
            $table->foreignUuid('unit_id')->constrained('units');

            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_targets');
    }
};
