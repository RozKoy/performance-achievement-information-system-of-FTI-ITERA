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
        Schema::create('iku_unit_statuses', function (Blueprint $table) {
            $table->uuid('id');

            $table->enum('status', ['blank']);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('indikator_kinerja_program_id')->constrained('indikator_kinerja_program');
            $table->foreignUuid('period_id')->constrained('iku_periods');
            $table->foreignUuid('unit_id')->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_unit_statuses');
    }
};
