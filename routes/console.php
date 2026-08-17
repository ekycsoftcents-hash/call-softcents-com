<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

Schedule::command('telescope:prune --hours=24')->daily();
Schedule::command('callers:sync-status')->everyThirtySeconds();
Schedule::command('calls:dispatch')->everyThirtySeconds();
Schedule::command('calls:reconcile')->everyThirtySeconds();
Schedule::command('campaign:launch')->everyThirtySeconds();
Schedule::command('campaign:finish')->everyThirtySeconds();
