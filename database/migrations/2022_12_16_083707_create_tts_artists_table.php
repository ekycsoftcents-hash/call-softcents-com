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
        Schema::create('tts_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tts_language_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');
            $table->enum('gender', ['male', 'female', 'neutral']);
            $table->boolean('enabled')->default(false);
            $table->timestamps();
            $table->unique(['tts_language_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tts_artists');
    }
};
