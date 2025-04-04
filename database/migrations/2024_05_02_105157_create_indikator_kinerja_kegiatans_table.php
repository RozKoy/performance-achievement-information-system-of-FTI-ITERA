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
        Schema::create('indikator_kinerja_kegiatan', function (Blueprint $table): void {
            $table->uuid('id');

            $table->unsignedInteger('number');
            $table->text('name');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('sasaran_kegiatan_id')->constrained('sasaran_kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kinerja_kegiatan');
    }
};
