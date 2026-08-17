<?php

declare(strict_types=1);

namespace App\Console\Commands\Callers;

use App\Models\Caller;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

#[Signature('callers:sync-status')]
#[Description('Synchronize caller availability with the FusionPBX Event Socket')]
final class SyncStatus extends Command
{
    public function handle(): int
    {
        /** @var Collection<int, Caller> $callers */
        $callers = Caller::syncable()
            ->whereRelation('server', 'enabled', true)
            ->with('server')
            ->get();

        foreach ($callers->groupBy('server_id') as $serverId => $serverCallers) {
            $server = $serverCallers->first()->server;
            $host = $server->fusionPbxEventSocketHost();
            $port = (int) ($server->freeswitch_event_socket_port ?: 8021);
            $errorNumber = 0;
            $errorMessage = '';
            $socket = @stream_socket_client("tcp://{$host}:{$port}", $errorNumber, $errorMessage, 3);
            $online = is_resource($socket);

            if ($online) {
                fclose($socket);
            } else {
                $this->warn("FusionPBX Event Socket unavailable for server {$serverId}: {$errorMessage}");
            }

            Caller::whereIn('id', $serverCallers->pluck('id'))->update([
                'is_online' => $online,
                'last_synced_at' => now(),
            ]);

            $this->info(sprintf('%d callers synchronized for FusionPBX server %s', $serverCallers->count(), $serverId));
        }

        return self::SUCCESS;
    }
}
