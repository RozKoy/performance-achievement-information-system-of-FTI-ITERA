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
        Schema::create('iku_achievement_data', function (Blueprint $table): void {
            $table->uuid('id');

            $table->text('data');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('achievement_id')->constrained('iku_achievements');
            $table->foreignUuid('column_id')->constrained('ikp_columns');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_achievement_data');
    }
};
