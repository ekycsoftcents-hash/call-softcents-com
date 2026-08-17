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
        Schema::create('audio', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('tts_artist_id')->nullable()->constrained('tts_artists')->nullOnDelete();

            $table->string('title');
            $table->text('description')->nullable();

            $table->string('type')->default('tts');
            $table->string('approval')->default('pending');
            $table->text('message')->nullable();

            // Paths to audio files
            $table->string('original_path')->nullable();
            $table->string('converted_path')->nullable();

            // Additional metadata
            $table->integer('duration')->nullable();
            $table->integer('size')->nullable();

            $table->string('conversion_status')->default('pending');
            $table->text('conversion_error')->nullable();

            $table->string('tts_status')->nullable();
            $table->text('tts_error')->nullable();

            $table->timestamp('converted_at')->nullable();
            $table->timestamp('tts_generated_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audio');
    }
};
