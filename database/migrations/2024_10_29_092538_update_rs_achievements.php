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
        Schema::table('rs_achievements', function (Blueprint $table): void {
            $table->text('link')->nullable()->after('realization');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rs_achievements', function (Blueprint $table): void {
            $table->dropColumn('link');
        });
    }
};
