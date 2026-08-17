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
        Schema::create('servers', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->enum('ari_scheme', ['http', 'https'])->default('http');
            $table->string('ari_host');
            $table->string('ari_port')->nullable();
            $table->string('ari_username');
            $table->string('ari_password');

            $table->string('database_host');
            $table->unsignedInteger('database_port')->default(3306);
            $table->string('database_username')->nullable();
            $table->string('database_password')->nullable();

            $table->boolean('enabled')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
