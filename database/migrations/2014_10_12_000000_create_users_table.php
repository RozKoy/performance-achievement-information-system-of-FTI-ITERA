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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id');

            $table->uuid('token')->nullable()->default(null);

            $table->string('password');
            $table->string('access');
            $table->string('email');
            $table->string('name');
            $table->string('role');

            $table->timestamps();
            $table->softDeletes();

            $table->primary('id');
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
