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
        Schema::create('callers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('server_id')->constrained('servers')->cascadeOnDelete();
            $table->string('caller_name');
            $table->string('caller_number');
            $table->string('trunk_name');
            $table->unsignedInteger('max_concurrency')->default(0);

            $table->boolean('is_online')->default(false);
            $table->boolean('enabled')->default(false);
            $table->timestamp('last_synced_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('callers');
    }
};
