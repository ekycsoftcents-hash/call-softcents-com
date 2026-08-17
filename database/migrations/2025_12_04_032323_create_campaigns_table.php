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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('audio_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('caller_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('group_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('description')->nullable();
            $table->string('status')->default('pending');
            $table->string('approval')->default('pending');
            $table->string('file_path')->nullable();
            $table->dateTime('scheduled_at')->nullable();
            $table->index(['user_id', 'status']);
            $table->index(['status', 'scheduled_at']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
