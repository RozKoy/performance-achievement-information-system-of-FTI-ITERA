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
        Schema::disableForeignKeyConstraints();

        Schema::table('iku_single_achievements', function (Blueprint $table): void {
            $table->uuid('unit_id')->nullable()->change();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('iku_single_achievements', function (Blueprint $table): void {
            $table->uuid('unit_id')->change();
        });

        Schema::enableForeignKeyConstraints();
    }
};
