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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            // $table->string('fullname');
            $table->string('firstnames', 50)->nullable();
            $table->string('lastname', 20)->nullable();
            $table->enum('gender', ['Feminin', 'Masculin']);
            $table->string('email', 255)->nullable()->unique();
            $table->string('phone', 16)->nullable()->unique();
            $table->string('photo', 45)->nullable();

            $table->string('status_id');

            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_verified_at')->nullable();
            $table->string('password');
            $table->uuid('locality_id')->nullable();
            $table->uuid('language_id')->nullable();
            $table->uuid('citoyen_id')->nullable();
            $table->boolean('is_send_otp')->default(true);
            $table->string('auth_provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->rememberToken();
            $table->uuid('created_by_id')->nullable();
            $table->uuid('updated_by_id')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
