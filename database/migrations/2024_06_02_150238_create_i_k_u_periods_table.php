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
        Schema::create('iku_periods', function (Blueprint $table): void {
            $table->uuid('id');

            $table->boolean('status');
            $table->char('period', 1);

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');

            $table->foreignUuid('deadline_id')->nullable()->constrained('iku_periods');
            $table->foreignUuid('year_id')->constrained('iku_years');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('iku_periods');
    }
};
