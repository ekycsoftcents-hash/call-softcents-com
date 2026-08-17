# Laravel 13 + RabbitMQ + FusionPBX/FreeSWITCH IVR Broadcast

## Implementation and live-integration guide

এই guide-এ project-এর বর্তমান architecture, RabbitMQ call queue, background worker, FusionPBX/FreeSWITCH Event Socket connection, live event synchronization এবং production deployment একসঙ্গে দেখানো হয়েছে। Project-এর final telephony layer হলো **FusionPBX/FreeSWITCH**; Asterisk/ARI ব্যবহার করা হয়নি।

> **Core idea:** Laravel HTTP request দ্রুত `202 Queued` response দেবে, RabbitMQ call job ধরে রাখবে, persistent worker job process করবে, এবং FreeSWITCH Event Socket FusionPBX call originate ও live event status পরিচালনা করবে।

## 1. Complete architecture

```text
Blade Admin / Reseller / Client portal
                |
                | POST call request
                v
Laravel 13 API / Controller
                |
                | dispatch(OriginateFusionPbxCall)
                v
RabbitMQ: outbound-calls queue
                |
                | persistent consumer
                v
Laravel queue worker
                |
                | ESL auth + originate/bgapi originate
                v
FusionPBX / FreeSWITCH
                |
                | CHANNEL_CREATE / ANSWER / HANGUP_COMPLETE
                v
Event listener -> call status update -> broadcast/WebSocket
```

Laravel queues are designed to move slow work out of the HTTP request and let a worker process it in the background. Laravel also allows separate named queues and priority ordering with `queue:work --queue=high,default` [1].

## 2. RabbitMQ setup

### 2.1 Environment variables

Copy the environment template and set real credentials. Do not commit passwords to Git.

```dotenv
APP_ENV=production
APP_URL=https://portal.example.com

QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=voice_worker
RABBITMQ_PASSWORD=use-a-long-random-password
RABBITMQ_VHOST=/voice
RABBITMQ_QUEUE=outbound-calls
RABBITMQ_CONSUME_MODE=poll
RABBITMQ_HEARTBEAT_CONNECTION=60
RABBITMQ_CONNECT_TIMEOUT=10
RABBITMQ_READ_TIMEOUT=120
RABBITMQ_WRITE_TIMEOUT=30
RABBITMQ_MAX_RETRIES=3
RABBITMQ_HEALTH_CHECK_ENABLED=true

FUSIONPBX_SCHEME=https
FUSIONPBX_DOMAIN=pbx.example.com
FREESWITCH_EVENT_SOCKET_HOST=10.20.0.15
FREESWITCH_EVENT_SOCKET_PORT=8021
FREESWITCH_EVENT_SOCKET_PASSWORD=use-a-separate-esl-password
FUSIONPBX_DEFAULT_CONTEXT=default
FUSIONPBX_OUTBOUND_GATEWAY=provider_gateway_name
```

In Docker Compose, the application container should use `rabbitmq` as the host, not `127.0.0.1`, because `127.0.0.1` inside the app container points back to that same container. The project already includes a persistent `rabbitmq:4-management-alpine` service, a RabbitMQ data volume, and a worker dependency on RabbitMQ.

### 2.2 Install and enable the driver

The project uses the native AMQP-compatible RabbitMQ driver already declared in `composer.json` and locked in `composer.lock`.

```bash
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
```

The PHP runtime must include the AMQP extension used by the Docker image. Build the supplied Docker image rather than testing with a local PHP runtime that is older than the project requirement.

### 2.3 Queue configuration

The relevant project configuration is:

```php
// config/queue.php
'rabbitmq' => [
    'driver' => 'rabbitmq',
    'queue' => env('RABBITMQ_QUEUE', 'outbound-calls'),
    'after_commit' => false,
],
```

Set `QUEUE_CONNECTION=rabbitmq` in production. For local development, `QUEUE_CONNECTION=sync` may be used only for a short functional test; it bypasses RabbitMQ and should not be used for real campaigns.

### 2.4 Dispatch a call job

A controller should validate the destination and dispatch only the minimum safe identifiers. Do not put a SIP password into the RabbitMQ payload.

```php
public function originate(Request $request): JsonResponse
{
    $data = $request->validate([
        'destination' => ['required', 'string', 'max:40'],
    ]);

    $client = $request->user();
    $caller = $client->callers()->where('enabled', true)->firstOrFail();

    OriginateFusionPbxCall::dispatch(
        clientId: $client->id,
        callerId: $caller->id,
        destination: $data['destination'],
    )->onConnection('rabbitmq')->onQueue('outbound-calls');

    return response()->json([
        'status' => 'queued',
        'message' => 'Call accepted for background processing.',
    ], 202);
}
```

The worker loads the encrypted SIP profile during execution. This avoids exposing credentials in HTTP logs, RabbitMQ management messages or serialized job payloads.

### 2.5 Start the worker

For a manual test:

```bash
php artisan rabbitmq:consume --queue=outbound-calls --consume-mode=poll
```

The project Docker worker uses Supervisor:

```ini
[program:rabbitmq-outbound-worker]
command=php /var/www/artisan rabbitmq:consume --queue=outbound-calls --consume-mode=poll
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/rabbitmq-worker.log
stopwaitsecs=3600
```

A production worker must be a persistent process, not a browser request or a low-frequency scheduled task. Supervisor should restart the process after a crash. Use more than one process only after checking FusionPBX gateway capacity and the client’s `max_concurrency` limit.

### 2.6 Retry and failure policy

The job should define bounded retries and a backoff. A typical call job is:

```php
public int $tries = 3;
public int $timeout = 90;

public function backoff(): array
{
    return [5, 30, 120];
}

public function failed(Throwable $exception): void
{
    // Mark the call failed, release any reservation, and broadcast failure.
}
```

RabbitMQ consumer acknowledgement must happen only after the Laravel job has completed successfully. RabbitMQ distinguishes consumer acknowledgements from publisher confirms; both should be considered when designing reliable delivery [2]. For call campaigns, use an idempotency key such as `call_uuid` and check it before originating, because a retry can otherwise create a duplicate call.

## 3. Live FusionPBX/FreeSWITCH connection

FusionPBX is the administration layer; FreeSWITCH is the call engine. Laravel connects to FreeSWITCH through `mod_event_socket`, which exposes TCP call control, API commands and the internal event system [3].

### 3.1 FreeSWITCH Event Socket configuration

On the FusionPBX/FreeSWITCH server, confirm `mod_event_socket` is loaded and configure a restricted listener:

```xml
<!-- autoload_configs/event_socket.conf.xml -->
<configuration name="event_socket.conf" description="Socket Client">
  <settings>
    <param name="listen-ip" value="10.20.0.15"/>
    <param name="listen-port" value="8021"/>
    <param name="password" value="use-a-long-esl-password"/>
    <param name="apply-inbound-acl" value="loopback.auto"/>
  </settings>
</configuration>
```

Do not expose port 8021 to the public Internet. Permit only the Laravel worker’s private IP through the FreeSWITCH ACL and firewall. The documentation notes that FreeSWITCH sends `auth/request`, accepts `auth <password>`, and returns `+OK accepted` after successful authentication [3].

### 3.2 Authentication handshake

The minimum Event Socket handshake is:

```text
TCP connect to FREESWITCH_EVENT_SOCKET_HOST:8021
< Content-Type: auth/request
> auth your-esl-password
< Reply-Text: +OK accepted
```

The project’s `app/Services/FusionPbx/FusionPbxClient.php` performs this handshake, creates a dial string such as `sofia/gateway/{gateway}/{destination}`, applies client Caller ID variables and sends the originate command.

### 3.3 Originate a call

A non-blocking Event Socket originate is preferable for campaign traffic:

```text
bgapi originate {origination_caller_id_name=Client A,origination_caller_id_number=8801XXXXXXXXX,accountcode=call-uuid}sofia/gateway/provider_gateway/017XXXXXXXX &park
```

FreeSWITCH returns a Job-UUID for `bgapi originate`; later `BACKGROUND_JOB` or channel events can be correlated to that identifier [3]. The current project service uses `api originate` for direct response handling. For high-volume campaigns, replace it with `bgapi originate` and persist the returned Job-UUID beside the Laravel call record.

Important: a SIP password should not be injected into arbitrary channel variables. FusionPBX should own the gateway/extension credential configuration, while Laravel selects the authorized client gateway/profile and sends Caller ID plus an internal `accountcode`. If the product requires client self-service credential provisioning, validate and encrypt the credential, then provision it into a controlled FusionPBX extension/gateway configuration rather than sending it in the originate command.

## 4. IVR broadcast outgoing flow

এই project-এর outbound call এখন সাধারণ `park` application-এ থামবে না। Answer হওয়ার পরে FreeSWITCH call-টিকে একটি controlled FusionPBX IVR extension/context-এ transfer করবে। IVR-এ recorded announcement, menu prompt, DTMF action, retry/timeout এবং hangup behaviour পরিচালিত হবে। FreeSWITCH-এর `transfer` application dialplan extension, dialplan type এবং context গ্রহণ করে [6]।

### 4.1 FusionPBX IVR configuration

FusionPBX-এ একটি dedicated IVR তৈরি করুন, উদাহরণস্বরূপ extension `5000` এবং context `default`। IVR-এ campaign audio prompt সেট করুন এবং প্রয়োজন হলে DTMF branches তৈরি করুন। একটি typical flow:

```text
Answer
  -> Play campaign announcement
  -> Wait for DTMF
  -> 1 = connect/transfer to configured destination
  -> 2 = repeat message
  -> timeout = hangup or repeat once
```

`.env`-এ Laravel worker যে IVR entry-তে transfer করবে তা নির্ধারণ করুন:

```dotenv
FUSIONPBX_IVR_EXTENSION=5000
FUSIONPBX_IVR_CONTEXT=default
```

Laravel job authenticated client-এর Caller ID এবং authorized FusionPBX gateway ব্যবহার করে destination-এ originate করবে, তারপর answer হলে এই IVR-এ transfer করবে। IVR audio file FusionPBX-এর media/sound storage-এ থাকতে হবে; Laravel-এর private storage path সরাসরি FreeSWITCH-এর `playback` path হিসেবে ব্যবহার করা যাবে না, যদি না shared filesystem বা controlled upload pipeline configured থাকে।

### 4.2 Originate command

Project-এর adapter restricted application command তৈরি করে:

```text
api originate {origination_caller_id_number=...,accountcode=call-id}sofia/gateway/provider_gateway/017XXXXXXXX &transfer(5000 XML default)
```

এখানে `5000` এবং `default` environment configuration থেকে আসে। Arbitrary command injection ঠেকাতে adapter শুধু `park` এবং `ivr:<extension>:<context>` format গ্রহণ করে। SIP password command, queue payload বা broadcast event-এ পাঠানো হয় না।

### 4.3 IVR broadcast controls

Campaign স্তরে অন্তত এই controls রাখা উচিত: per-client rate limit, maximum concurrent calls, answer timeout, IVR repeat count, DTMF action, quiet hours এবং opt-out/DNC suppression। Regulatory বা carrier policy অনুযায়ী consent ও opt-out handling আলাদা করে implement করতে হবে; এই guide আইনগত পরামর্শ নয়।

## 5. Live event synchronization

### 4.1 Subscribe to channel events

A long-lived Event Socket listener should authenticate once and subscribe to the events needed for call state:

```text
event plain CHANNEL_CREATE CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE BACKGROUND_JOB
```

The inbound ESL documentation describes subscriptions such as `CHANNEL_CREATE CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE` for a complete call lifecycle [4]. The events catalog should be used to confirm available headers and event names [5].

### 4.2 Listener responsibilities

The listener should:

1. Keep one authenticated TCP connection per FusionPBX server.
2. Reconnect with exponential backoff after a disconnect.
3. Parse `Content-Length` and event headers correctly; do not assume one TCP read equals one event.
4. Extract `Unique-ID`, `Job-UUID`, `Event-Name`, `Caller-Caller-ID-Number`, `Caller-Destination-Number`, `variable_accountcode`, `Hangup-Cause` and timestamps.
5. Find the Laravel call using `call_uuid`, `job_uuid` or `accountcode`.
6. Update status idempotently; a duplicate event must not charge twice.
7. Publish a Laravel broadcast event after the database transaction commits.

A simplified event mapping is:

| FreeSWITCH event | Laravel call status |
|---|---|
| `BACKGROUND_JOB` with successful originate | `accepted` or `originated` |
| `CHANNEL_CREATE` | `ringing` |
| `CHANNEL_ANSWER` | `answered` |
| `CHANNEL_HANGUP_COMPLETE` with normal cause | `completed` |
| `CHANNEL_HANGUP_COMPLETE` with failure cause | `failed` |
| socket disconnect or timeout | `unknown` until reconciled |

### 4.3 Listener pseudocode

```php
while (true) {
    $socket = connectAndAuthenticate($server);
    writeCommand($socket, 'event plain CHANNEL_CREATE CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE BACKGROUND_JOB');

    while (is_resource($socket) && ! feof($socket)) {
        $event = readEslFrame($socket); // parse headers + Content-Length body
        if ($event === null) {
            continue;
        }

        $call = findCallByCorrelationKey($event);
        if (! $call) {
            logUnmatchedEvent($event);
            continue;
        }

        DB::transaction(function () use ($call, $event): void {
            updateCallStatusIdempotently($call, mapEventToStatus($event));
            FusionPbxCallStatusUpdated::dispatch($call->fresh())->afterCommit();
        });
    }

    sleepWithExponentialBackoff();
}
```

Run the listener as a separate persistent Supervisor program rather than inside the HTTP process:

```ini
[program:fusionpbx-event-listener]
command=php /var/www/artisan fusionpbx:events
numprocs=1
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/storage/logs/fusionpbx-events.log
stopwaitsecs=3600
```

The current project has the originate client and call job. A production event synchronizer should be added as the `fusionpbx:events` command so that status updates continue even when no browser is open.

## 6. Broadcast to the Blade portals

After updating a call record, broadcast a small, non-sensitive payload:

```php
final class FusionPbxCallStatusUpdated implements ShouldBroadcast
{
    public function __construct(public readonly Call $call) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('client.'.$this->call->user_id.'.calls')];
    }

    public function broadcastWith(): array
    {
        return [
            'call_id' => $this->call->id,
            'status' => $this->call->status->value,
            'destination' => $this->call->phone_number,
            'updated_at' => $this->call->updated_at?->toISOString(),
        ];
    }
}
```

Never broadcast SIP passwords, Event Socket passwords, full gateway credentials or internal server addresses. The client channel must authorize only the owning client; reseller channels should authorize only clients whose `reseller_id` matches the authenticated reseller.

## 7. Docker deployment

Use the project’s Docker Compose services:

```bash
cp .env.example .env
# Set APP_KEY, database, RabbitMQ and FusionPBX credentials.
docker compose build
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
docker compose logs -f worker
```

The RabbitMQ management UI is normally available on port 15672, but it should be bound to a private administration network or protected by a reverse proxy and strong credentials. Confirm queue depth, unacked messages, worker logs and FusionPBX Event Socket connectivity before sending a campaign.

## 8. Operational checklist

| Check | Expected result |
|---|---|
| RabbitMQ TCP connection | Laravel worker can connect to the private broker host |
| Queue depth | Messages are consumed and unacked count returns to zero |
| Worker restart | Supervisor restarts a deliberately stopped worker |
| ESL authentication | Laravel receives `+OK accepted` |
| Originate | FusionPBX gateway creates a call and returns UUID/Job-UUID |
| Event sync | Answer and hangup events update the correct Laravel call |
| Idempotency | Replayed event does not create a second charge or call |
| Tenant isolation | Client A cannot access Client B’s call, caller or credential |
| Credential safety | Passwords are encrypted and absent from logs/events |
| Failure handling | Failed jobs are recorded and retry counts are bounded |

## References

[1]: https://laravel.com/docs/13.x/queues "Laravel 13 Queues documentation"
[2]: https://www.rabbitmq.com/docs/confirms "RabbitMQ Consumer Acknowledgements and Publisher Confirms"
[3]: https://developer.signalwire.com/freeswitch/integration/event-socket/ "FreeSWITCH Event Socket documentation"
[4]: https://developer.signalwire.com/freeswitch/programming/esl-inbound/ "FreeSWITCH Inbound Event Socket documentation"
[5]: https://developer.signalwire.com/freeswitch/programming/events-catalog/ "FreeSWITCH Events Catalog"
[6]: https://developer.signalwire.com/freeswitch/FreeSWITCH-Explained/Modules/mod-dptools/ "FreeSWITCH transfer application"

## 9. A–Z Docker deployment topology

আপনার requirement অনুযায়ী application-এর সব runtime Docker-এ থাকবে। তবে production-এ সব container একই Docker host-এ রাখা বাধ্যতামূলক নয়। Recommended topology হলো দুইটি private Docker stack:

| Docker stack | Containers | Role |
|---|---|---|
| Application stack | `web`, `app`, `worker`, `event-listener`, `cdr-reconciler`, `db`, `redis`, `rabbitmq`, `minio` | Portal, queue, event processing, billing এবং storage |
| PBX stack | `fusionpbx`, `freeswitch`, `pbx-db` বা integrated PBX database | SIP gateway, IVR, media, call routing, CDR |

Application stack এবং PBX stack private network/VPN-এর মাধ্যমে কথা বলবে। Laravel worker শুধু FusionPBX Event Socket port `8021`-এ connect করবে; SIP/RTP ports public application network-এ expose করা হবে না।

### 9.1 Application stack services

Project-এর `docker-compose.yml` ইতিমধ্যে Laravel PHP-FPM, Nginx, MySQL, Redis, MinIO, RabbitMQ এবং Supervisor worker service রাখে। Production hardening-এর জন্য:

```yaml
services:
  app:
    restart: unless-stopped
    env_file: .env
    depends_on:
      db:
        condition: service_healthy
      redis:
        condition: service_started
      rabbitmq:
        condition: service_healthy

  worker:
    restart: unless-stopped
    env_file: .env
    command: ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
    depends_on:
      rabbitmq:
        condition: service_healthy

  rabbitmq:
    image: rabbitmq:4-management-alpine
    restart: unless-stopped
    expose:
      - "5672"
    # Publish 15672 only on a private admin interface.
```

RabbitMQ, MySQL, Redis এবং MinIO public Internet-এ publish করবেন না। Nginx কেবল `80/443` publish করবে। Supervisor-এর অধীনে আলাদা process রাখা হবে: RabbitMQ consumer, FusionPBX ESL event listener, CDR reconciliation scheduler এবং Laravel scheduler।

### 9.2 PBX stack decision

FusionPBX হলো FreeSWITCH-এর administration layer এবং PBX engine হলো FreeSWITCH। FusionPBX-এর জন্য একটি universally maintained official production Docker image ধরে নেওয়া ঠিক নয়; public community images-এর version, security patch এবং licensing যাচাই না করে production-এ ব্যবহার করা উচিত নয়। FusionPBX-এর official quick-install documentation-এ supported Debian installation path অনুসরণ করা যায় [7]।

সুতরাং production-এর জন্য দুইটি valid option আছে:

| Option | সুবিধা | ব্যবহার |
|---|---|---|
| Custom FusionPBX Docker image | Full A–Z container control, reproducible build | Dev/staging বা আপনার DevOps team image maintain করলে |
| Dedicated PBX host with Docker-managed services | SIP/RTP stability, easier firewall and monitoring | Production IVR broadcast-এর জন্য recommended |

যদি FusionPBX-ও Docker container-এ রাখতে চান, custom image-এ FusionPBX, FreeSWITCH, `mod_event_socket`, sound files, IVR definitions এবং SIP/RTP configuration version-control করতে হবে। Image tag immutable রাখুন; `latest` ব্যবহার করবেন না। PBX container-এর জন্য persistent volumes রাখুন:

```text
pbx-config       -> /etc/freeswitch
pbx-sounds       -> /usr/share/freeswitch/sounds
pbx-recordings   -> /var/lib/freeswitch/recordings
pbx-cdr          -> configured CDR database/export
```

### 9.3 Required network rules

| Source | Destination | Port | Rule |
|---|---|---:|---|
| Laravel worker | FreeSWITCH Event Socket | 8021/TCP | Allow only from worker private IP |
| FreeSWITCH | SIP provider | Provider-defined | Allow SIP signalling and RTP range |
| Laravel app | RabbitMQ | 5672/TCP | Private Docker network only |
| Admin VPN | RabbitMQ management | 15672/TCP | Optional, private admin only |
| Browser | Nginx | 443/TCP | Public HTTPS entry point |

FreeSWITCH SIP/RTP requires provider-specific ports and firewall rules. The exact range depends on the PBX configuration and carrier; do not copy a random RTP range into production.

### 9.4 A–Z deployment sequence

```bash
# Application host
cp .env.example .env
# Set APP_KEY, database, RabbitMQ, MinIO and FusionPBX private endpoint.
docker compose build --pull
docker compose up -d db redis rabbitmq minio
docker compose run --rm app php artisan key:generate
docker compose run --rm app php artisan migrate --force
docker compose up -d app web worker

# Verify app stack
docker compose ps
docker compose logs --tail=100 worker

docker compose exec app php artisan optimize:clear

# PBX host: start the version-pinned FusionPBX/FreeSWITCH stack,
# then validate Event Socket authentication, gateway, IVR and media.

# Final controlled test
# 1. Create one test contact.
# 2. Dispatch one IVR broadcast call.
# 3. Confirm answer -> IVR prompt -> DTMF.
# 4. Confirm hangup event and CDR.
# 5. Confirm one billing ledger entry.
```

The first production campaign must not start until the complete chain is verified: **RabbitMQ job → ESL originate → answer → IVR media → DTMF/timeout → hangup event → CDR → final balance deduction**.

[7]: https://docs.fusionpbx.com/en/latest/getting_started/quick_install.html "FusionPBX Quick Install documentation"

## 10. FusionPBX/FreeSWITCH Docker stack

A separate `docker-compose.pbx.yml` blueprint has been added to the project. It defines:

- `pbx-db`: PostgreSQL with a persistent volume;
- `fusionpbx`: a locally built, version-pinned FusionPBX/FreeSWITCH image;
- persistent volumes for FreeSWITCH configuration, IVR sounds, recordings, FusionPBX web files and logs;
- private app-network connectivity for Laravel Event Socket access;
- SIP, HTTPS, Event Socket and configurable RTP port mappings;
- healthchecks for PostgreSQL and FreeSWITCH.

The PBX compose file intentionally does **not** silently select an unverified `latest` community image. Before production, build and audit `docker/pbx/Dockerfile`, pin the FusionPBX and FreeSWITCH versions, verify the license/source of every package, and set the real SIP/RTP port range used by the carrier. The resulting image should be published to a private registry and referenced through `FUSIONPBX_IMAGE`.

Example start sequence:

```bash
# Create the shared external network once if app and PBX are on one Docker host.
docker network create call_softcents_app-network || true

# Start the application stack.
docker compose up -d

# Start the audited PBX stack.
docker compose -f docker-compose.pbx.yml build --pull
docker compose -f docker-compose.pbx.yml up -d

# Check PBX and Event Socket health.
docker compose -f docker-compose.pbx.yml ps
docker compose -f docker-compose.pbx.yml logs --tail=100 fusionpbx
```

If the PBX runs on another Docker host, do not use a local Docker external network across hosts. Use a private VPN or private routed network, set `FREESWITCH_EVENT_SOCKET_HOST` to the PBX private address, and permit port `8021/TCP` only from the Laravel worker address.

## 11. Two broadcast modes

Platform-এ এখন দুইটি campaign mode থাকবে। Laravel-এর `calls.broadcast_mode` field প্রতিটি call-কে `voice` অথবা `ivr` হিসেবে চিহ্নিত করবে। নতুন migration এবং `BroadcastMode` enum এই distinction enforce করে।

| Mode | Answer-এর পরে behavior | FusionPBX route |
|---|---|---|
| **Voice Broadcast** | Recorded campaign message বাজবে; message শেষ হলে call hangup হবে | `FUSIONPBX_VOICE_EXTENSION` / `FUSIONPBX_VOICE_CONTEXT` |
| **IVR Broadcast** | Recorded message-এর পরে DTMF menu, repeat, transfer, timeout বা opt-out branch থাকবে | `FUSIONPBX_IVR_EXTENSION` / `FUSIONPBX_IVR_CONTEXT` |

### 11.1 Voice Broadcast

FusionPBX-এ একটি one-way dialplan/extension তৈরি করুন, উদাহরণস্বরূপ `5001`. এই route campaign audio playback করবে এবং playback শেষে hangup করবে। Laravel worker answer call-কে এই route-এ transfer করবে:

```text
&transfer(5001 XML default)
```

Voice Broadcast-এ DTMF menu থাকবে না। Customer call ধরলে message শুনবে, তারপর call শেষ হবে।

### 11.2 IVR Broadcast

FusionPBX-এ একটি IVR extension তৈরি করুন, উদাহরণস্বরূপ `5000`. এই route announcement বাজিয়ে DTMF input অপেক্ষা করবে:

```text
&transfer(5000 XML default)
```

একটি example IVR flow হলো: `1` চাপলে agent/number-এ transfer, `2` চাপলে message repeat, `9` চাপলে opt-out/DNC list-এ যোগ, এবং timeout হলে hangup বা one-time repeat।

### 11.3 Environment configuration

```dotenv
FUSIONPBX_VOICE_EXTENSION=5001
FUSIONPBX_VOICE_CONTEXT=default
FUSIONPBX_IVR_EXTENSION=5000
FUSIONPBX_IVR_CONTEXT=default
```

দুই mode-এর audio prompt FusionPBX/FreeSWITCH-এর accessible sound storage-এ থাকতে হবে। Laravel private object-storage path সরাসরি `playback` path হিসেবে ব্যবহার করা যাবে না; controlled PBX media sync বা shared volume দরকার।

### 11.4 Billing distinction

দুই mode-ই একই CDR billing pipeline ব্যবহার করবে। Voice Broadcast-এ answered call এবং playback completion final status নির্ধারণে গুরুত্বপূর্ণ। IVR Broadcast-এ `ivr_started`, DTMF action, transfer এবং opt-out event অতিরিক্ত metadata হিসেবে রাখতে হবে। Final cost `billsec`/CDR থেকে হিসাব হবে; queued বা failed call-এর জন্য campaign policy অনুযায়ী charge/refund হবে।
