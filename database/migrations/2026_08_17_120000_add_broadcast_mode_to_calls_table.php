<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calls', function (Blueprint $table): void {
            $table->string('broadcast_mode')->default('voice')->after('type')->index();
            $table->string('ivr_extension')->nullable()->after('broadcast_mode');
            $table->string('ivr_context')->nullable()->after('ivr_extension');
        });
    }

    public function down(): void
    {
        Schema::table('calls', function (Blueprint $table): void {
            $table->dropIndex(['broadcast_mode']);
            $table->dropColumn(['broadcast_mode', 'ivr_extension', 'ivr_context']);
        });
    }
};
