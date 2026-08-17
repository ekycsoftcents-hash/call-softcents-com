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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('prev_status')->nullable()->after('status');
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->string('prev_status')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn('prev_status');
        });

        Schema::table('calls', function (Blueprint $table) {
            $table->dropColumn('prev_status');
        });
    }
};
