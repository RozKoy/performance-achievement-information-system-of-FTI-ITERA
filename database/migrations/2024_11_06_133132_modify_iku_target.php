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
        Schema::table('iku_targets', function (Blueprint $table): void {
            $table->double('target')->change();
        });

        Schema::table('iku_evaluations', function (Blueprint $table): void {
            $table->double('target')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
