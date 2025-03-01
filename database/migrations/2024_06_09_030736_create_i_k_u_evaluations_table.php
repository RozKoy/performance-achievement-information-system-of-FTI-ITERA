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
        Schema::create('iku_evaluations', function (Blueprint $table): void {
            $table->uuid('id');

            $table->string('evaluation')->nullable();
            $table->string('follow_up')->nullable();
            $table->boolean('status');
            $table->integer('target');

            $table->timestamps();
            $table->softDeletes();

            $table->foreignUuid('indikator_kinerja_program_id')->constrained('indikator_kinerja_program');

            $table->primary('id');
            $table->unique('indikator_kinerja_program_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_evaluations');
    }
};
