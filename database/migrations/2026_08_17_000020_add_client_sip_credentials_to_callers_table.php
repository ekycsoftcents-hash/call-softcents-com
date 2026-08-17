<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('callers', function (Blueprint $table): void {
            $table->string('sip_username', 120)->nullable()->after('trunk_name');
            $table->text('sip_password')->nullable()->after('sip_username');
            $table->string('sip_domain', 255)->nullable()->after('sip_password');
            $table->unsignedSmallInteger('sip_port')->nullable()->after('sip_domain');
            $table->string('sip_context', 80)->default('from-internal')->after('sip_port');
        });
    }

    public function down(): void
    {
        Schema::table('callers', function (Blueprint $table): void {
            $table->dropColumn([
                'sip_username',
                'sip_password',
                'sip_domain',
                'sip_port',
                'sip_context',
            ]);
        });
    }
};
