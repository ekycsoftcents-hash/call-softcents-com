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
        Schema::table('calls', function (Blueprint $table) {
            $table->decimal('cost', 15, 6)->default(0)->change();
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->decimal('amount', 15, 6)->default(0)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount', 15, 6)->default(0)->change();
            $table->decimal('balance_before', 15, 6)->default(0)->change();
            $table->decimal('balance_after', 15, 6)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->decimal('cost', 10, 4)->default(0)->change();
        });

        Schema::table('deposits', function (Blueprint $table) {
            $table->decimal('amount', 12, 4)->default(0)->change();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->decimal('amount', 15, 4)->default(0)->change();
            $table->decimal('balance_before', 15, 4)->default(0)->change();
            $table->decimal('balance_after', 15, 4)->default(0)->change();
        });
    }
};
