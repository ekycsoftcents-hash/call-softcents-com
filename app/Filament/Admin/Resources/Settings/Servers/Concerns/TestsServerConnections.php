<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Servers\Concerns;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

trait TestsServerConnections
{
    protected function testServerConnections(array $data): void
    {
        $errors = [];

        $host = $data['freeswitch_event_socket_host'] ?? null;
        $port = (int) ($data['freeswitch_event_socket_port'] ?? 8021);

        if (! $host) {
            $errors['freeswitch_event_socket_host'] = 'FreeSWITCH Event Socket host is required.';
        } else {
            try {
                $socket = @stream_socket_client("tcp://{$host}:{$port}", $errorNumber, $errorMessage, 5);

                if (! is_resource($socket)) {
                    $errors['freeswitch_event_socket_host'] = 'Could not connect to FusionPBX Event Socket: '.$errorMessage;
                } else {
                    fclose($socket);
                }
            } catch (Throwable $e) {
                $errors['freeswitch_event_socket_host'] = 'FusionPBX Event Socket connection failed: '.$e->getMessage();
            }
        }

        try {
            config(['database.connections._server_test' => [
                ...config('database.connections.mysql'),
                'host' => $data['database_host'],
                'port' => (int) $data['database_port'],
                'username' => $data['database_username'],
                'password' => $data['database_password'],
            ]]);

            DB::purge('_server_test');
            DB::connection('_server_test')->getPdo();
        } catch (Throwable $e) {
            $errors['database_host'] = 'Database connection failed: '.$e->getMessage();
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }
}
