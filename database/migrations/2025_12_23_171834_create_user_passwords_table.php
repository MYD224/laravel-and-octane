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
        Schema::create('user_passwords', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('security_policy_id');
            $table->uuid('user_id');
            $table->string('type'); //a mettre a jour pour prendre un type enum
            $table->string('password', 255);
            $table->date('expired_at');
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
        Schema::dropIfExists('user_passwords');
    }
};
