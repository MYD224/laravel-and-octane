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
        Schema::create('structure_menu_overides', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('structure_id');
            $table->uuid('menu_item_id');
            $table->string('icon')->nullable();
            $table->json('custom_label')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->nullable();
            $table->unique(['structure_id', 'menu_item_id']);

            $table->uuid('created_by_id');
            $table->uuid('last_updated_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();


            $table->foreign('structure_id')->references('id')->on('structures')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('menu_items')->cascadeOnDelete();
            $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('last_updated_by_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structure_menu_overides');
    }
};
