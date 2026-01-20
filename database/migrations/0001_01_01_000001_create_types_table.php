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
        Schema::create('types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('category', [
                'Batiment',
                'Carriere',
                'Document',
                'Emplacement',
                'Piece',
                'Redevable',
                'Support',
                'Taxe',
                'TaxeSupPub',
                'Structure'
            ]);
            $table->string('label', 45);
            $table->string('code', 10);
            $table->string('created_by_id');
            $table->string('updated_by_id');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('types');
    }
};
