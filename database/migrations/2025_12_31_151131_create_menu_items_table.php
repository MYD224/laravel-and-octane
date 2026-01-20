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
            $table->uuid('module_id')->nullable();
            $table->uuid('parent_id')->nullable();
            $table->string('code')->unique(); // The resource identifier (e.g., 'invoices')
            $table->enum('type', ['menu', 'tab'])->default('menu');
            $table->string('route_path');
            $table->string('icon')->nullable();
            $table->json('default_label'); // {"en": "Invoices", "fr": "Factures"}
            $table->integer('sort_order')->default(0);

            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->fk('module_id', 'modules')->cascadeOnDelete();
            $table->fk('parent_id', 'menu_items')->cascadeOnDelete();
            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
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
