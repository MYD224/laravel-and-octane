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
        Schema::table('users', function (Blueprint $table) {
            $table->fk('status_id', 'statuses')->cascadeOnDelete();
            $table->fk('locality_id', 'districts')->cascadeOnDelete();
            $table->fk('language_id', 'languages')->cascadeOnDelete();
            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
        });

        // Schema::table('user_statuses', function (Blueprint $table) {

        //     $table->uuid('created_by_id')->nullable();
        //     $table->uuid('updated_by_id')->nullable();

        //     $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
        //     $table->foreign('updated_by_id')->references('id')->on('users')->cascadeOnDelete();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_statuses', function (Blueprint $table) {
            $table->dropColumn(['locality_id', 'language_id', 'created_by_id', 'updated_by_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['created_by_id', 'updated_by_id']);
        });
    }
};
