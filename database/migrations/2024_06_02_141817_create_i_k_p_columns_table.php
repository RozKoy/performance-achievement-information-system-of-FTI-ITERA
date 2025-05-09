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
        Schema::create('ikp_columns', function (Blueprint $table): void {
            $table->uuid('id');

            $table->boolean('file')->default(false);
            $table->unsignedInteger('number');
            $table->string('name', 500);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('indikator_kinerja_program_id')->constrained('indikator_kinerja_program');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ikp_columns');
    }
};
