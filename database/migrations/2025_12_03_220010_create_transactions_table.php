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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('transactionable');

            $table->string('type')->index();
            $table->string('currency')->default('BDT');
            $table->decimal('amount', 15, 4)->default(0);
            $table->decimal('balance_before', 15, 4)->default(0);
            $table->decimal('balance_after', 15, 4)->default(0);

            $table->string('description');

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
