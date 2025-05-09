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
        Schema::create('iku_years', function (Blueprint $table): void {
            $table->uuid('id');

            $table->char('year', 4);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->unique('year');
        });

        Schema::table('sasaran_kegiatan', function (Blueprint $table): void {
            $table->foreignUuid('time_id')->constrained('iku_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::disableForeignKeyConstraints();

        Schema::table('sasaran_kegiatan', function (Blueprint $table): void {
            $table->dropForeign(['time_id']);
            $table->dropColumn('time_id');
        });

        Schema::dropIfExists('iku_years');
    }
};
