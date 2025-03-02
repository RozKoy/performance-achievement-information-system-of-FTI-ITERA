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
        Schema::table('iku_periods', function (Blueprint $table): void {
            $table->date('deadline')->nullable()->after('period');

            $table->dropForeign(['deadline_id']);
            $table->dropColumn('deadline_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('iku_periods', function (Blueprint $table): void {
            $table->dropColumn('deadline');

            $table->foreignUuid('deadline_id')->nullable()->constrained('iku_periods');
        });
    }
};
