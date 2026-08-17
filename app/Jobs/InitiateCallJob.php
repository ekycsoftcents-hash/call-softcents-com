<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Enums\BroadcastMode;
use App\Enums\CallStatus;
use App\Jobs\Concerns\RefundsCallCost;
use App\Models\Call;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FusionPbx\FusionPbxClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

final class InitiateCallJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable, RefundsCallCost;

    public int $uniqueFor = 60;

    private ?Call $call = null;

    public function __construct(
        public readonly int $callId
    ) {}

    public function uniqueId(): string
    {
        return (string) $this->callId;
    }

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->call = Call::with(['audio', 'caller.server', 'user'])
            ->withoutGlobalScopes()
            ->find($this->callId);

        if (! $this->call) {
            Log::warning("Call ID {$this->callId} not found in queue");

            return;
        }

        // Already settled — nothing to do.
        if (in_array($this->call->status, [CallStatus::Failed, CallStatus::Completed, CallStatus::Processing], true)) {
            return;
        }

        if (! $this->call->user) {
            $this->refundCallCost($this->callId, 'User not found');

            return;
        }

        if (! $this->call->audio) {
            $this->refundCallCost($this->callId, 'Audio record not found');

            return;
        }

        if (! $this->call->caller?->server) {
            $this->refundCallCost($this->callId, 'Caller/server configuration not found');

            return;
        }

        if (! $this->call->caller->sip_username || ! $this->call->caller->sip_password) {
            $this->refundCallCost($this->callId, 'Client SIP credential configuration not found');

            return;
        }

        $audioPath = $this->call->audio->converted_path;

        if (! $audioPath || ! Storage::exists($audioPath)) {
            $this->refundCallCost($this->callId, 'Audio file not found');

            return;
        }

        $this->initiateCall();
    }

    public function failed(Throwable $exception): void
    {
        try {
            $this->refundCallCost($this->callId, 'Job permanently failed: '.$exception->getMessage());
        } catch (Throwable $e) {
            Log::error('Refund failed during failed() handler', [
                'call_id' => $this->callId,
                'exception' => $e->getMessage(),
            ]);
        }

        Log::error('ProcessMarketingCall job failed', [
            'call_id' => $this->callId,
            'exception' => $exception->getMessage(),
        ]);
    }

    /**
     * @throws Throwable
     */
    private function initiateCall(): void
    {
        try {
            $server = $this->call->caller->server;
            $application = $this->call->broadcast_mode === BroadcastMode::Ivr
                ? sprintf(
                    'ivr:%s:%s',
                    $this->call->ivr_extension ?: config('services.fusionpbx.ivr_extension', '5000'),
                    $this->call->ivr_context ?: config('services.fusionpbx.ivr_context', 'default'),
                )
                : sprintf(
                    'voice:%s:%s',
                    config('services.fusionpbx.voice_extension', '5001'),
                    config('services.fusionpbx.voice_context', 'default'),
                );

            // Voice Broadcast plays the configured recording and ends.
            // IVR Broadcast transfers the answered call into the FusionPBX IVR.
            $uniqueId = app(FusionPbxClient::class)->originate(
                $server,
                $this->call->caller,
                $this->call->phone_number,
                $application
            );

            if (! $uniqueId) {
                $this->refundCallCost($this->callId, 'FusionPBX did not return a call identifier');

                return;
            }

            $this->call->update([
                'unique_id' => $uniqueId,
                'status' => CallStatus::Processing,
            ]);
        } catch (Exception $e) {
            Log::error("FusionPBX originate failed for Call ID {$this->call->id}", [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->refundCallCost($this->callId, 'FusionPBX server exception: '.$e->getMessage());
        }
    }
}
