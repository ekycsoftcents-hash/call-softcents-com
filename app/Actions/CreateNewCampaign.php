<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\CallInterface;
use App\Enums\CallStatus;
use App\Enums\CallType;
use App\Enums\CampaignApproval;
use App\Enums\TransactionType;
use App\Models\Call;
use App\Models\Campaign;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Throwable;

final class CreateNewCampaign
{
    /**
     * Create a new call and reserve its estimated cost.
     *
     * @throws Throwable
     */
    public function handle(User $user, array $input, CallInterface $interface = CallInterface::Web): Campaign
    {
        return DB::transaction(function () use ($user, $input, $interface): Campaign {
            $campaign = $user->campaigns()
                ->create(array_merge($input, [
                    'approval' => $user->auto_approve_campaigns
                        ? CampaignApproval::Approved
                        : CampaignApproval::Pending,
                ]));

            $campaign->loadMissing([
                'audio',
                'group' => fn (BelongsTo $q) => $q->withCount('contacts'),
            ]);

            $lockedUser = User::whereKey($user->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $campaign->audio) {
                throw new Exception('Audio not found');
            }

            $costPerCall = $campaign->audio->cost;
            $totalCost = $costPerCall * $campaign->group->contacts_count;

            if (! $lockedUser->hasEnoughBalance($totalCost)) {
                throw new Exception('Insufficient balance');
            }

            $lockedUser->decrement('balance', $totalCost);

            $campaign->transactions()->create([
                'user_id' => $lockedUser->id,
                'type' => TransactionType::Debit,
                'amount' => $totalCost,
                'balance_before' => $lockedUser->balance + $totalCost,
                'balance_after' => $lockedUser->balance,
                'currency' => 'BDT',
                'description' => "Reserved balance for campaign {$campaign->id}",
            ]);

            // We could move this logic to a queue job if we want to speed up the response time of campaign creation
            $now = now();
            $campaign->group->contacts()->chunkById(1000, function ($contacts) use ($campaign, $costPerCall, $interface, $now) {
                $payload = [];

                foreach ($contacts as $contact) {
                    $payload[] = [
                        'user_id' => $campaign->user_id,
                        'campaign_id' => $campaign->id,
                        'caller_id' => $campaign->caller_id,
                        'audio_id' => $campaign->audio_id,
                        'contact_id' => $contact->id,
                        'phone_number' => $contact->phone_number,
                        'status' => CallStatus::Pending,
                        'type' => CallType::Marketing,
                        'interface' => $interface,
                        'scheduled_at' => $campaign->scheduled_at,
                        'cost' => $costPerCall,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                Call::insert($payload);
            });

            return $campaign;
        });
    }
}
