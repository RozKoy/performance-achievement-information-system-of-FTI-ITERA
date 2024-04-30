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
        Schema::create('rs_time', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('status', 11);
            $table->string('period', 1);
            $table->string('year', 4);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
        });

        Schema::table('sasaran_strategis', function (Blueprint $table) {
            $table->foreignUuid('deadline_id')->constrained('rs_time');
            $table->foreignUuid('time_id')->constrained('rs_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rs_time');
    }
};
