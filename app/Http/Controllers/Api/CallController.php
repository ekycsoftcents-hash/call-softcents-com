<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateNewCall;
use App\Enums\CallInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Call\StoreCallRequest;
use App\Http\Resources\CallResource;
use App\Models\Call;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Throwable;

#[Group('Calls', 'Manage calls')]
#[Authenticated]
final class CallController extends Controller
{
    #[Endpoint(title: 'List Calls')]
    #[ResponseFromApiResource(CallResource::class, Call::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user)
    {
        $calls = Call::query()
            ->whereUserId($user->id)
            ->latest()
            ->paginate();

        return CallResource::collection($calls);
    }

    #[Endpoint(title: 'Create Call')]
    #[ResponseFromApiResource(CallResource::class, Call::class, status: 201)]
    public function store(#[CurrentUser] User $user, StoreCallRequest $request)
    {
        try {
            $call = app(CreateNewCall::class)
                ->handle($user, $request->validated(), CallInterface::API);

            return new CallResource($call->unsetRelation('user'));
        } catch (Throwable) {
            return response()->json([
                'message' => 'Call Creation Failed',
                'errors' => ['general' => ['Something went wrong while creating the call. Please try again.']],
            ], 500);
        }
    }
}
