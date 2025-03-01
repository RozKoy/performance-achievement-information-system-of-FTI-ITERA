<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('indikator_kinerja_program', function (Blueprint $table): void {
            $table->enum('mode', IndikatorKinerjaProgram::getModeValues())->default(IndikatorKinerjaProgram::MODE_TABLE)->after('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('indikator_kinerja_program', function (Blueprint $table): void {
            $table->dropColumn('mode');
        });
    }
};
