<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servers', function (Blueprint $table): void {
            $table->string('fusionpbx_scheme', 10)->default('https')->after('name');
            $table->string('fusionpbx_domain')->nullable()->after('fusionpbx_scheme');
            $table->string('freeswitch_event_socket_host')->nullable()->after('fusionpbx_domain');
            $table->unsignedSmallInteger('freeswitch_event_socket_port')->default(8021)->after('freeswitch_event_socket_host');
            $table->text('freeswitch_event_socket_password')->nullable()->after('freeswitch_event_socket_port');
        });
    }

    public function down(): void
    {
        Schema::table('servers', function (Blueprint $table): void {
            $table->dropColumn([
                'fusionpbx_scheme',
                'fusionpbx_domain',
                'freeswitch_event_socket_host',
                'freeswitch_event_socket_port',
                'freeswitch_event_socket_password',
            ]);
        });
    }
};
