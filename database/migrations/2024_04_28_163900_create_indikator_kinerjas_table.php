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
        Schema::create('indikator_kinerja', function (Blueprint $table): void {
            $table->uuid('id');

            $table->unsignedInteger('number');
            $table->string('status', 11);
            $table->string('type', 6);
            $table->text('name');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('kegiatan_id')->constrained('kegiatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indikator_kinerja');
    }
};
