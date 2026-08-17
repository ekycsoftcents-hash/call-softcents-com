# Research findings

## Laravel queues

Laravel 13 provides a unified queue API and supports multiple queue backends through `config/queue.php`. A connection can contain multiple named queues, and workers can prioritize queues using `php artisan queue:work --queue=high,default`. Laravel Horizon is designed for Redis-powered queues, so a RabbitMQ deployment should use the RabbitMQ driver/consumer and Supervisor or another process manager for worker lifecycle.

Source: https://laravel.com/docs/13.x/queues

## FreeSWITCH Event Socket

FreeSWITCH `mod_event_socket` exposes a TCP interface for API commands, call control and real-time events. Inbound clients connect to the Event Socket, receive `Content-Type: auth/request`, send `auth <password>`, then receive `Reply-Text: +OK accepted` before issuing commands. The default port is 8021, and the documentation recommends restricting exposure with `listen-ip` and `apply-inbound-acl` when listening beyond localhost.

The Event Socket can issue `bgapi originate` for non-blocking call origination and return a Job-UUID. Clients can subscribe to events using commands such as `event plain CHANNEL_CREATE CHANNEL_ANSWER CHANNEL_HANGUP_COMPLETE`, then correlate event headers such as Unique-ID, Caller-Caller-ID-Number and variable_accountcode with the Laravel call record.

Source: https://developer.signalwire.com/freeswitch/integration/event-socket/
Source: https://developer.signalwire.com/freeswitch/programming/esl-inbound/
Source: https://developer.signalwire.com/freeswitch/programming/events-catalog/

## Project-specific architecture

The project is Laravel 13 with a RabbitMQ queue driver, `OriginateFusionPbxCall`/call initiation jobs, FusionPBX/FreeSWITCH Event Socket client service, per-client encrypted SIP credential fields, Caller ID fields, reseller and client tenant relations, and lightweight Blade Admin/Reseller/Client portals. Asterisk/ARI was removed from the final runtime path. The project contains Docker Compose RabbitMQ service and Supervisor worker configuration.
