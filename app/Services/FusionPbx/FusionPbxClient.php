<?php

declare(strict_types=1);

namespace App\Services\FusionPbx;

use App\Models\Caller;
use App\Models\Server;
use RuntimeException;

final class FusionPbxClient
{
    public function originate(Server $server, Caller $caller, string $destination, string $application = 'park'): string
    {
        $socket = $this->connect($server);

        try {
            // FreeSWITCH sends auth/request before accepting commands.
            $this->read($socket);
            $this->write($socket, 'auth '.($server->freeswitch_event_socket_password ?? ''));
            $reply = $this->read($socket);

            if (! str_contains($reply, 'accepted')) {
                throw new RuntimeException('FusionPBX Event Socket authentication failed.');
            }

            $variables = [
                'origination_caller_id_name' => $caller->caller_name,
                'origination_caller_id_number' => $caller->caller_number,
                'effective_caller_id_name' => $caller->caller_name,
                'effective_caller_id_number' => $caller->caller_number,
                'sip_auth_realm' => $caller->sip_domain ?: $server->fusionpbx_domain,
                'accountcode' => (string) ($caller->id),
                'ignore_early_media' => 'true',
                'originate_timeout' => '300',
            ];

            $dialString = $this->dialString($server, $caller, $destination);
            $command = sprintf(
                'api originate %s%s %s',
                $this->channelVariables($variables),
                $dialString,
                $this->applicationCommand($application)
            );

            $this->write($socket, $command);
            $response = $this->read($socket);

            if (str_contains(strtolower($response), '-err')) {
                throw new RuntimeException(trim($response));
            }

            return trim($response);
        } finally {
            fclose($socket);
        }
    }

    /**
     * Build a restricted post-answer application. Supported values are `park`
     * `voice:<extension>:<context>`, which transfers to a one-way voice
     * broadcast dialplan entry, and `ivr:<extension>:<context>`, which
     * transfers into a FusionPBX/FreeSWITCH IVR dialplan entry.
     */
    private function applicationCommand(string $application): string
    {
        if ($application === 'park') {
            return 'park';
        }

        foreach (['voice:', 'ivr:'] as $prefix) {
            if (! str_starts_with($application, $prefix)) {
                continue;
            }

            [, $extension, $context] = array_pad(explode(':', $application, 3), 3, '');

            if ($extension === '' || $context === '' || ! preg_match('/^[a-zA-Z0-9_.-]+$/', $extension.$context)) {
                throw new RuntimeException('Invalid FusionPBX broadcast extension or context.');
            }

            return sprintf('&transfer(%s XML %s)', $extension, $context);
        }

        throw new RuntimeException('Unsupported FusionPBX originate application.');
    }

    private function connect(Server $server)
    {
        $host = $server->fusionPbxEventSocketHost();
        $port = (int) ($server->freeswitch_event_socket_port ?: 8021);
        $errorNumber = 0;
        $errorMessage = '';
        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errorNumber,
            $errorMessage,
            10
        );

        if (! is_resource($socket)) {
            throw new RuntimeException("Unable to connect to FusionPBX Event Socket: {$errorMessage}");
        }

        stream_set_timeout($socket, 30);
        return $socket;
    }

    private function dialString(Server $server, Caller $caller, string $destination): string
    {
        $domain = $caller->sip_domain ?: $server->fusionpbx_domain;
        $gateway = $caller->trunk_name;

        if ($gateway) {
            return sprintf('sofia/gateway/%s/%s', $this->escape($gateway), $this->escape($destination));
        }

        return sprintf('user/%s@%s', $this->escape($destination), $this->escape($domain));
    }

    private function channelVariables(array $variables): string
    {
        $pairs = [];
        foreach ($variables as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            $pairs[] = $key.'='.$this->escape((string) $value);
        }

        return $pairs === [] ? '' : '{'.implode(',', $pairs).'} ';
    }

    private function escape(string $value): string
    {
        return str_replace(['\\', ',', '}', '{'], ['\\\\', '\\,', '\\}', '\\{'], $value);
    }

    private function write($socket, string $command): void
    {
        fwrite($socket, $command."\n\n");
    }

    private function read($socket): string
    {
        $reply = '';
        while (! feof($socket)) {
            $line = fgets($socket);
            if ($line === false) {
                break;
            }
            $reply .= $line;
            if (rtrim($line) === '') {
                break;
            }
        }

        return $reply;
    }
}
