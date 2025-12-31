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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name')->unique(); // The resource identifier (e.g., 'invoices')
            $table->enum('type', ['menu', 'tab'])->default('menu');
            $table->string('route_path');
            $table->string('icon')->nullable();
            $table->json('default_label'); // {"en": "Invoices", "fr": "Factures"}
            $table->integer('sort_order')->default(0);

            $table->uuid('created_by_id')->nullable();
            $table->uuid('last_updated_by_id')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('parent_id')->references('id')->on('menu_items')->cascadeOnDelete();
            $table->foreign('created_by_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('last_updated_by_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};
