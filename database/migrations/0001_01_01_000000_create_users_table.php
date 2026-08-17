<?php

declare(strict_types=1);

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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('avatar_url')->nullable();

            $table->enum('type', ['admin', 'user'])->default('user');
            $table->enum('status', ['pending', 'approved', 'rejected', 'banned'])->default('pending');
            $table->string('audio_type')->default('upload');
            $table->boolean('auto_approve_audio')->default(false);
            $table->boolean('auto_approve_campaigns')->default(false);

            $table->decimal('balance', 15, 4)->default(0);
            $table->decimal('pulse_rate', 15, 4)->default(0.10);
            $table->unsignedInteger('pulse_duration')->default(10);
            $table->string('company_name')->nullable();
            $table->string('front_nid')->nullable();
            $table->string('back_nid')->nullable();

            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
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
