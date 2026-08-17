# Laravel 13 + RabbitMQ + FusionPBX/FreeSWITCH Calling Setup

এই project Laravel 13-এ রাখা হয়েছে। Outbound call engine হিসেবে **শুধু FusionPBX/FreeSWITCH** ব্যবহার করা হয়েছে; Asterisk বা ARI final call path-এর অংশ নয়। Laravel call records, campaign, balance এবং client profile পরিচালনা করবে। RabbitMQ call jobs queue করবে এবং persistent worker FreeSWITCH Event Socket-এর মাধ্যমে FusionPBX server-এ originate command পাঠাবে।

## Architecture

```text
Client campaign
    -> Laravel 13 call record
    -> RabbitMQ outbound-calls queue
    -> Laravel RabbitMQ worker
    -> encrypted client SIP profile + Caller ID load
    -> FreeSWITCH Event Socket on FusionPBX
    -> FusionPBX gateway / extension
    -> outbound call
```

## Runtime

Project-এর Dockerfile PHP 8.4 ব্যবহার করে। প্রথমবার setup:

```bash
cp .env.example .env
# .env-এ application, database, RabbitMQ ও FusionPBX secrets বসান
docker compose build
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --force
docker compose exec app php artisan storage:link
```

## RabbitMQ

`.env`-এ broker settings বসান:

```dotenv
QUEUE_CONNECTION=rabbitmq
RABBITMQ_HOST=rabbitmq
RABBITMQ_PORT=5672
RABBITMQ_USER=voice_worker
RABBITMQ_PASSWORD=change-me
RABBITMQ_VHOST=/voice
RABBITMQ_QUEUE=outbound-calls
RABBITMQ_CONSUME_MODE=poll
RABBITMQ_MAX_RETRIES=3
```

Docker Compose RabbitMQ 4 management service এবং persistent volume তৈরি করে। Management UI সাধারণত port `15672`-এ থাকে। Production-এ management port public internet-এ expose না করে private network বা VPN-এর মধ্যে রাখুন।

Worker Supervisor-এর মাধ্যমে চালু থাকে। Manual test-এর জন্য:

```bash
php artisan rabbitmq:consume --queue=outbound-calls --consume-mode=poll
```

## FusionPBX/FreeSWITCH

FusionPBX server-এ `mod_event_socket` enabled থাকতে হবে এবং Event Socket listener application server থেকে reachable হতে হবে। `.env`-এ Event Socket settings বসান:

```dotenv
FUSIONPBX_SCHEME=https
FUSIONPBX_DOMAIN=pbx.example.com
FREESWITCH_EVENT_SOCKET_HOST=10.0.0.20
FREESWITCH_EVENT_SOCKET_PORT=8021
FREESWITCH_EVENT_SOCKET_PASSWORD=your-event-socket-password
FUSIONPBX_DEFAULT_CONTEXT=default
```

FusionPBX-এ outbound gateway ও dialplan আগে তৈরি করতে হবে। Caller profile-এর `trunk_name` field FusionPBX gateway name হিসেবে ব্যবহৃত হবে। Gateway না থাকলে service `user/{destination}@{domain}` endpoint ব্যবহার করবে; PSTN/mobile call-এর জন্য configured gateway ব্যবহার করাই স্বাভাবিক।

## Client-specific SIP profile

প্রতিটি Caller record-এ আলাদা `sip_username`, `sip_password`, `sip_domain`, `sip_port`, `sip_context`, `trunk_name`, `caller_name` এবং `caller_number` রাখা যায়। `sip_password` Eloquent encrypted cast দিয়ে database-এ encrypted থাকে। Password log, queue payload বা API response-এ পাঠানো হয় না।

প্রত্যেক client নিজের profile dashboard বা authenticated endpoint থেকে সংরক্ষণ করবে। Call request থেকে caller ID গ্রহণ না করে application authenticated client-এর assigned Caller record load করবে। ফলে Client A-এর request Client B-এর SIP profile বা Caller ID ব্যবহার করতে পারবে না।

FreeSWITCH originate command-এ client-specific `origination_caller_id_name`, `origination_caller_id_number`, SIP realm এবং account code পাঠানো হয়। SIP provider-এর authentication FusionPBX gateway/extension configuration-এ valid হতে হবে; application সরাসরি provider-এ SIP registration করে না।

## Queue job ও call status

Call request database record তৈরি করে `InitiateCallJob` dispatch করবে। RabbitMQ worker job গ্রহণ করে audio, caller, server এবং client credential profile যাচাই করবে। Profile অসম্পূর্ণ হলে job call না করে configured refund/failure logic চালাবে। Valid profile হলে worker FreeSWITCH Event Socket-এ `auth` করে `api originate` command পাঠাবে এবং returned identifier call record-এ সংরক্ষণ করবে।

Call status broadcast layer দিয়ে queued, processing, accepted এবং failed state dashboard-এ প্রকাশ করা যাবে। Production realtime UI-এর জন্য configured Laravel broadcast provider অথবা WebSocket server প্রয়োজন হবে।

## Security checklist

FusionPBX Event Socket port শুধুমাত্র application server-এর private IP থেকে allow করুন। Default password পরিবর্তন করুন, TLS/VPN ব্যবহার করুন যেখানে সম্ভব, এবং `.env` বা secret manager ছাড়া credential commit করবেন না। Client SIP password encrypted database cast-এ রাখুন এবং logs-এ request payload বা password লিখবেন না। Caller assignment authorization চালু রাখুন, যাতে client অন্য client-এর Caller profile নির্বাচন করতে না পারে।

## Validation status

`composer.json` এবং `composer.lock` Laravel 13 ও `iamfarhad/laravel-rabbitmq` 1.4.2-এর সঙ্গে synchronized। Asterisk ARI Composer package ও Supervisor manager process সরানো হয়েছে। FusionPBX service, server/client migrations, RabbitMQ config, Docker broker এবং changed PHP files syntax-lint করা হয়েছে। Live FusionPBX call test করার জন্য real domain, Event Socket password, gateway এবং client SIP profile প্রয়োজন।

## White-label reseller portal

The portal hierarchy is **Super Admin → Reseller → Reseller Client/User**. A reseller can manage only its own client records, campaigns, balances, Caller ID profiles and call reports. Client records carry `reseller_id`, so a reseller cannot access another reseller's client data through the reseller API.

The reseller portal page is available at `/reseller-portal` for authenticated reseller users. It displays the reseller brand, client count, active client count, configured domain and recent clients. The user panel dynamically resolves the reseller logo, favicon and brand name from the active branding profile.

Authenticated reseller API endpoints are:

```text
GET  /api/reseller/branding
PUT  /api/reseller/branding
GET  /api/reseller/clients
POST /api/reseller/clients
```

A reseller can configure `brand_name`, `logo_url`, `favicon_url`, `primary_color`, `secondary_color`, support contact details, `custom_domain` and `subdomain`. DNS for a custom domain must point to the application reverse proxy. A subdomain can be used when custom-domain DNS is not required.

To create a client, the reseller sends name, email and password to `POST /api/reseller/clients`. The server automatically assigns `reseller_id`, marks the account as a client user and never accepts a reseller ID from the request body.

## Lightweight Laravel Blade reseller portal

The reseller-facing portal is now available as a fast Laravel Blade interface instead of a Filament page. The main dashboard is `/reseller`, branding settings are available at `/reseller/branding`, and client creation is handled directly through the Blade form. The portal uses a dedicated Tailwind/Vite entry (`resources/css/reseller.css`) so the reseller pages do not load the large Filament stylesheet or Filament JavaScript bundle.

The existing Filament admin panel can remain available for Super Admin operations, while reseller users use the lightweight Blade portal. This preserves the existing administrative tools without making the reseller-facing experience dependent on Filament.

## Lightweight Blade Admin portal

The Admin panel is now available at `/admin` as a Laravel Blade interface protected by the `admin` middleware. The Filament Admin panel provider has been deregistered, so `/admin` no longer loads Filament. The Blade Admin portal includes dashboard statistics, reseller creation/listing, client listing, FusionPBX server listing, caller profiles, call records and deposits.

The reseller-facing portal remains available at `/reseller`. Both portals use Laravel controllers, Blade templates and the dedicated lightweight Tailwind/Vite bundle. The existing Filament package files may remain in the repository for compatibility with the user panel and package ecosystem, but the Admin and Reseller pages do not load Filament assets.

## Lightweight Blade Client portal

Client users now use the Blade portal at `/client`. The dashboard shows account balance, total calls, campaigns and assigned Caller profiles. Separate pages list the client's own calls and campaigns, and `/client/callers` allows the client to update its own Caller ID and SIP profile. The controller verifies ownership through the `caller_user` relation before allowing any profile update.

The final portal hierarchy is **Admin (`/admin`) → Reseller (`/reseller`) → Client (`/client`)**. Login routing sends reseller users to `/reseller`, while client users can use `/client`; all client data queries remain scoped to the authenticated user's `user_id`.
