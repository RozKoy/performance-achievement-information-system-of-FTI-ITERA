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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->uuid('id');

            $table->unsignedInteger('number');
            $table->text('name');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('sasaran_strategis_id')->constrained('sasaran_strategis');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
