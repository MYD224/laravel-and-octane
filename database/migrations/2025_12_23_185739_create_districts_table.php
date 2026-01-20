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
        //table representant les quartiers et districts
        Schema::create('districts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label', 50);
            $table->string('code', 5)->nullable();
            $table->uuid('municipality_id');
            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->fk('municipality_id', 'municipalities')->cascadeOnDelete();
            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('districts');
    }
};
