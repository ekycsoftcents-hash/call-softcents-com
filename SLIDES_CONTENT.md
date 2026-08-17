# Laravel 13 + RabbitMQ + FusionPBX/FreeSWITCH

## Slide 1 — Project overview

**White-label multi-tenant IVR broadcast outgoing platform**

Laravel 13, lightweight Blade portals, RabbitMQ background processing এবং FusionPBX/FreeSWITCH IVR telephony engine।

Hierarchy: **Admin → Reseller → Client**

## Slide 2 — Business problem

IVR broadcast campaign synchronous HTTP request-এ চালালে web request block হয়, duplicate call ও status mismatch তৈরি হতে পারে। Answer-এর পরে recorded announcement, DTMF menu, repeat/timeout এবং opt-out flow-ও নির্ভরযোগ্যভাবে চালানো দরকার।

## Slide 3 — Solution architecture

```text
Blade portals → Laravel 13 → RabbitMQ → Worker → FreeSWITCH Event Socket → FusionPBX gateway → PSTN/SIP destination → IVR transfer
                                      ↑                         ↓
                              retry + failure              live call events
                                      └──────── Call status + billing ────────┘
```

## Slide 4 — Three portal model

**Admin portal:** reseller, client, FusionPBX server, caller profile, calls এবং deposits পরিচালনা করে।

**Reseller portal:** নিজের brand, domain, client, balance, campaign এবং reports পরিচালনা করে।

**Client portal:** নিজের balance, IVR campaign, contacts, Caller ID, encrypted SIP profile, calls এবং live IVR status দেখে।

## Slide 5 — RabbitMQ call lifecycle

1. Client call request পাঠায়।
2. Laravel call record তৈরি করে job dispatch করে।
3. RabbitMQ `outbound-calls` queue job ধরে রাখে।
4. Persistent worker client profile load করে।
5. Worker FusionPBX Event Socket-এ originate করে এবং answer হলে IVR extension/context-এ transfer করে।
6. IVR announcement, DTMF action, repeat/timeout এবং opt-out branch পরিচালিত হয়।
7. Retry, failure এবং idempotency duplicate call প্রতিরোধ করে।

## Slide 6 — FusionPBX/FreeSWITCH live connection

FreeSWITCH `mod_event_socket` একটি authenticated TCP interface। Laravel প্রথমে `auth <password>` দিয়ে authenticate করে, তারপর `api originate` চালিয়ে answer call-কে `&transfer(5000 XML default)`-এর মতো FusionPBX IVR entry-তে পাঠায়। IVR-এ recorded announcement ও DTMF menu থাকে; SIP password RabbitMQ payload বা log-এ রাখা হয় না।

## Slide 7 — Event synchronization

Laravel-এর long-lived Event Socket listener `CHANNEL_CREATE`, `CHANNEL_ANSWER`, `CHANNEL_HANGUP_COMPLETE` এবং `BACKGROUND_JOB` subscribe করে। Event-এর `Unique-ID`, `Job-UUID` এবং `accountcode` দিয়ে call record match হয়। Status: queued → originating → ringing → answered → ivr_started → dtmf/timeout → completed/failed।

## Slide 8 — Security and tenant isolation

Client credential database-এ encrypted। Client request থেকে reseller ID গ্রহণ করা হয় না; authenticated user relation থেকে ownership resolve হয়। Event Socket port private network/ACL-এ সীমাবদ্ধ। Broadcast channel private এবং client/reseller ownership check করে। SIP ও Event Socket password কখনও browser, queue payload, log বা broadcast-এ পাঠানো হয় না।

## Slide 9 — Deployment and operations

Docker Compose RabbitMQ broker ও persistent volume চালায়। Supervisor RabbitMQ worker, FusionPBX event listener এবং scheduler auto-restart করে। Deployment steps: `.env` configure → Docker build → migrations → cache clear → worker logs verify → test call → event/status validation। Monitoring: queue depth, unacked messages, failed jobs, worker restart, ESL connectivity এবং unmatched events।

## Slide 10 — Outcome and next steps

Platform outcome: fast Blade experience, asynchronous IVR broadcast, per-client identity, recorded announcement + DTMF flow, real-time status, reseller white-labeling এবং secure FusionPBX integration।

Next steps: production FusionPBX credentials, gateway/dialplan validation, event listener command deployment, WebSocket broadcaster configuration, rate limits, billing rules এবং load test।

**References:** Laravel Queues [1], RabbitMQ Confirms [2], FreeSWITCH Event Socket [3], Inbound ESL [4], Events Catalog [5].

[1]: https://laravel.com/docs/13.x/queues
[2]: https://www.rabbitmq.com/docs/confirms
[3]: https://developer.signalwire.com/freeswitch/integration/event-socket/
[4]: https://developer.signalwire.com/freeswitch/programming/esl-inbound/
[5]: https://developer.signalwire.com/freeswitch/programming/events-catalog/
