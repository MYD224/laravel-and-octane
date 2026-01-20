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
        Schema::create('structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('email', 50)->nullable();
            $table->string('main_phone', 16)->nullable();
            $table->string('secondary_phone', 16)->nullable();
            $table->string('address')->nullable();
            $table->string('website', 50)->nullable();
            $table->uuid('status_id');
            $table->uuid('type_id'); // mairie, syndicat, prestataire, autre
            $table->string('logo', 70)->nullable();
            $table->boolean('is_owner')->default(false);
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->uuid('prefecture_id')->nullable();
            $table->uuid('created_by_id');
            $table->uuid('updated_by_id');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->fk('type_id', 'types')->cascadeOnDelete();
            $table->fk('status_id', 'statuses')->cascadeOnDelete();
            $table->fk('created_by_id', 'users')->cascadeOnDelete();
            $table->fk('updated_by_id', 'users')->cascadeOnDelete();
            $table->fk('prefecture_id', 'prefectures')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('structures');
    }
};
