<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('gateway');
            $table->string('event_id');
            $table->string('event_type');
            $table->string('status');
            $table->json('payload');
            $table->json('headers');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
    }
};
