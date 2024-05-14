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
        Schema::create('rs_periods', function (Blueprint $table) {
            $table->uuid('id');

            $table->boolean('status');
            $table->char('period', 1);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('year_id')->constrained('rs_years');
            $table->foreignUuid('deadline_id')->constrained('rs_periods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rs_periods');
    }
};
