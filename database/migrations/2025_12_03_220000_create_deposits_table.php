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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 4)->default(0);
            $table->string('currency')->default('BDT');
            $table->string('payment_method')->nullable(); // piprapay, manual
            $table->string('transaction_id')->nullable()->unique(); // payment gateway er id
            $table->string('status')->default('pending'); // pending, completed, failed, refunded
            $table->json('meta')->nullable(); // gateway response payload
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deposits');
    }
};
