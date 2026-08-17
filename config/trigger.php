<?php

declare(strict_types=1);

use App\Support\Trigger\Subscribers\Heartbeat;

return [
    // Auto-detect databases and tables from registered routes.
    'detect' => (bool) env('TRIGGER_DETECT', true),

    // Optional manual filters (used when detect is disabled or to extend it).
    'databases' => env('TRIGGER_DATABASES', '') ? explode(',', env('TRIGGER_DATABASES')) : [],
    'tables' => env('TRIGGER_TABLES', '') ? explode(',', env('TRIGGER_TABLES')) : [],

    'heartbeat' => (int) env('TRIGGER_HEARTBEAT', 3),

    // Periodically ping the MySQL metadata connection to avoid server-side idle disconnects.
    // Set to 0 to disable.
    'keepalive' => (int) env('TRIGGER_KEEPALIVE', 0),

    // MySQL session variables to apply on connect (for the metadata connection).
    // Example:
    // - wait_timeout=7200,interactive_timeout=7200
    'session_variables' => env('TRIGGER_SESSION_VARIABLES', '')
        ? array_filter(array_map('trim', explode(',', (string) env('TRIGGER_SESSION_VARIABLES'))))
        : [],

    'subscribers' => [
        Heartbeat::class,
    ],

    'route' => app()->basePath('routes/trigger.php'),

    // Supervisor: how often (seconds) to poll the servers table for changes.
    'check_interval' => (int) env('TRIGGER_CHECK_INTERVAL', 5),
];
