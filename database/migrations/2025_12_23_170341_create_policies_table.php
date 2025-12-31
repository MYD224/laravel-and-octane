<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label', 45);
            $table->string('code', 10);
            $table->string('status', 10); // a mettre a jour plus tard en se referant aux status deja existants

            $table->uuid('created_by_id');
            $table->uuid('last_updated_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('last_updated_by_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};
