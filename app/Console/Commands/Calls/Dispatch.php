<?php

declare(strict_types=1);

namespace App\Console\Commands\Calls;

use App\Enums\CallStatus;
use App\Enums\CampaignStatus;
use App\Jobs\InitiateCallJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

#[Signature('calls:dispatch')]
#[Description('Dispatch pending calls onto the queue, respecting server/caller concurrency limits')]
final class Dispatch extends Command
{
    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $dispatchedIds = DB::transaction(function (): array {
            $pending = CallStatus::Pending->value;
            $processing = CallStatus::Processing->value;
            $initiated = CallStatus::Initiated->value;
            $campaignProcessing = CampaignStatus::Processing->value;

            $sql = <<<'SQL'
                WITH eligible_calls AS (
                    SELECT
                        c.id,
                        c.caller_id,
                        cl.server_id,
                        ROW_NUMBER() OVER (
                            PARTITION BY c.caller_id
                            ORDER BY c.id
                        ) AS caller_rank
                    FROM calls c
                    JOIN callers cl ON cl.id = c.caller_id
                    LEFT JOIN campaigns ca ON ca.id = c.campaign_id
                    WHERE c.status = ?
                    AND cl.enabled = 1
                    AND cl.is_online = 1
                    AND (
                        (
                            c.campaign_id IS NOT NULL
                            AND ca.status = ?
                            AND (ca.scheduled_at IS NULL OR ca.scheduled_at <= NOW())
                        )
                        OR
                        (
                            c.campaign_id IS NULL
                            AND (c.scheduled_at IS NULL OR c.scheduled_at <= NOW())
                        )
                    )
                ),
                caller_limits AS (
                    SELECT
                        cl.id AS caller_id,
                        GREATEST(0, cl.max_concurrency - COALESCE(p.count, 0)) AS remaining
                    FROM callers cl
                    LEFT JOIN (
                        SELECT caller_id, COUNT(*) AS count
                        FROM calls
                        WHERE status IN (?, ?)
                        GROUP BY caller_id
                    ) p ON p.caller_id = cl.id
                ),
                server_limits AS (
                    SELECT
                        cl.server_id,
                        GREATEST(0, s.max_concurrency - COALESCE(p.count, 0)) AS remaining
                    FROM callers cl
                    JOIN servers s ON s.id = cl.server_id
                    LEFT JOIN (
                        SELECT cl2.server_id, COUNT(*) AS count
                        FROM calls c
                        JOIN callers cl2 ON cl2.id = c.caller_id
                        WHERE c.status IN (?, ?)
                        GROUP BY cl2.server_id
                    ) p ON p.server_id = cl.server_id
                    GROUP BY cl.server_id, s.max_concurrency, p.count
                ),
                caller_filtered AS (
                    SELECT
                        e.id,
                        e.caller_id,
                        e.server_id
                    FROM eligible_calls e
                    JOIN caller_limits cl ON cl.caller_id = e.caller_id
                    WHERE e.caller_rank <= cl.remaining
                ),
                server_ranked AS (
                    SELECT
                        cf.id,
                        cf.caller_id,
                        cf.server_id,
                        ROW_NUMBER() OVER (
                            PARTITION BY cf.server_id
                            ORDER BY cf.id
                        ) AS server_rank
                    FROM caller_filtered cf
                )
                SELECT sr.id
                FROM server_ranked sr
                JOIN server_limits sl ON sl.server_id = sr.server_id
                WHERE sr.server_rank <= sl.remaining
                ORDER BY sr.id
            SQL;

            $rows = DB::select($sql, [
                $pending,
                $campaignProcessing,
                $processing, $initiated,
                $processing, $initiated,
            ]);

            if (empty($rows)) {
                return [];
            }

            $ids = array_column($rows, 'id');

            $updateSql = sprintf(
                'UPDATE calls SET status = ?, initiated_at = NOW() WHERE status = ? AND id IN (%s)',
                implode(',', array_fill(0, count($ids), '?'))
            );

            DB::update($updateSql, [$initiated, $pending, ...$ids]);

            foreach ($ids as $id) {
                InitiateCallJob::dispatch($id)->onQueue('calling');
            }

            return $ids;
        });

        if (empty($dispatchedIds)) {
            $this->info('No calls to dispatch.');

            return;
        }

        $this->info('Dispatched '.count($dispatchedIds).' calls onto the queue.');
    }
}
