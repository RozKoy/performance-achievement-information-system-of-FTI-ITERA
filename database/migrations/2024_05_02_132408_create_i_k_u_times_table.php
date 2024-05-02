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
        Schema::create('iku_time', function (Blueprint $table) {
            $table->uuid('id');

            $table->string('status', 11);
            $table->char('period', 1);
            $table->char('year', 4);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
        });

        Schema::table('sasaran_kegiatan', function (Blueprint $table) {
            $table->foreignUuid('deadline_id')->constrained('iku_time');
            $table->foreignUuid('time_id')->constrained('iku_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iku_time');
    }
};