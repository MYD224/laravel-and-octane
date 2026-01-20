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
        Schema::create('structure_menu_overrides', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('structure_id');
            $table->uuid('menu_item_id');
            $table->string('icon')->nullable();
            $table->json('custom_label')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->nullable();
            $table->unique(['structure_id', 'menu_item_id']);

            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();


            $table->fk('structure_id', 'structures')->cascadeOnDelete();
            $table->fk('menu_item_id', 'menu_items')->cascadeOnDelete();
            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
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
