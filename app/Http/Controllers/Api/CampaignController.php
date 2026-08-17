<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateNewCampaign;
use App\Enums\CallInterface;
use App\Enums\CampaignStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\StoreCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Http\Resources\CampaignResource;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Throwable;

#[Group('Campaigns', 'Manage voice campaigns')]
#[Authenticated]
final class CampaignController extends Controller
{
    #[Endpoint(title: 'List Campaigns')]
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user)
    {
        $campaigns = Campaign::whereUserId($user->id)
            ->with(['audio', 'group'])
            ->latest()
            ->paginate(15);

        return CampaignResource::collection($campaigns);
    }

    #[Endpoint(title: 'Create Campaign')]
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, status: 201)]
    public function store(#[CurrentUser] User $user, StoreCampaignRequest $request)
    {
        try {
            $campaign = app(CreateNewCampaign::class)
                ->handle($user, $request->validated(), CallInterface::API);

            return new CampaignResource($campaign->load(['audio', 'group']));
        } catch (Throwable) {
            return response()->json([
                'message' => 'Campaign Creation Failed',
                'errors' => ['general' => ['Something went wrong while creating the campaign. Please try again.']],
            ], 500);
        }
    }

    #[Endpoint(title: 'Get Campaign')]
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class, with: ['audio', 'group'])]
    public function show(#[CurrentUser] User $user, Campaign $campaign)
    {
        if ($campaign->user_id !== $user->id) {
            abort(403);
        }

        $campaign->load(['audio', 'group']);

        return new CampaignResource($campaign);
    }

    #[Endpoint(title: 'Update Campaign')]
    #[ResponseFromApiResource(CampaignResource::class, Campaign::class)]
    #[Response(
        status: 403,
        description: 'You do not have permission to update this campaign, or the campaign cannot be updated due to its current status.',
    )]
    public function update(#[CurrentUser] User $user, UpdateCampaignRequest $request, Campaign $campaign)
    {
        abort_if($campaign->user_id !== $user->id, 403, 'You do not have permission to update this campaign.');
        abort_if($campaign->status !== CampaignStatus::Pending, 403, 'Only pending campaigns can be updated.');
        abort_if($campaign->scheduled_at && $campaign->scheduled_at->isPast(), 403, 'Cannot update a campaign that has already been executed.');

        $campaign->update($request->validated());

        return new CampaignResource($campaign->load(['audio', 'group']));
    }

    #[Endpoint(title: 'Delete Campaign')]
    public function destroy(#[CurrentUser] User $user, Campaign $campaign)
    {
        if ($campaign->user_id !== $user->id) {
            abort(403);
        }

        $campaign->delete();

        return response()->noContent();
    }
}
