<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Group\StoreGroupRequest;
use App\Http\Requests\Group\UpdateGroupRequest;
use App\Http\Resources\GroupResource;
use App\Models\Group;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group as ScribeGroup;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[ScribeGroup('Groups', 'Manage groups')]
#[Authenticated]
final class GroupController extends Controller
{
    #[Endpoint(title: 'List Groups', description: 'Retrieve a list of groups for the current user.')]
    #[ResponseFromApiResource(name: GroupResource::class, model: User::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user): ResourceCollection
    {
        $groups = Group::whereUserId($user->id)
            ->withCount('contacts')
            ->latest()
            ->paginate();

        return GroupResource::collection($groups);
    }

    #[Endpoint(title: 'Create Group', description: 'Create a new group.')]
    #[ResponseFromApiResource(name: GroupResource::class, model: Group::class, status: 201)]
    #[Response(content: ['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]], status: 422)]
    public function store(#[CurrentUser] User $user, StoreGroupRequest $request)
    {
        $group = $user->groups()->create($request->validated());

        return new GroupResource($group);
    }

    #[Endpoint(title: 'Get Group', description: 'Retrieve a specific group.')]
    #[ResponseFromApiResource(name: GroupResource::class, model: Group::class)]
    #[Response(content: ['message' => 'This action is unauthorized.'], status: 403)]
    #[Response(content: ['message' => 'Record not found.'], status: 404)]
    public function show(#[CurrentUser] User $user, Group $group)
    {
        if ($group->user_id !== $user->id) {
            abort(403);
        }

        return new GroupResource($group->loadCount('contacts'));
    }

    #[Endpoint(title: 'Update Group', description: 'Update a specific group.')]
    #[ResponseFromApiResource(GroupResource::class, Group::class)]
    #[Response(['message' => 'This action is unauthorized.'], 403)]
    #[Response(['message' => 'Record not found.'], 404)]
    #[Response(['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]], 422)]
    public function update(#[CurrentUser] User $user, UpdateGroupRequest $request, Group $group)
    {
        if ($group->user_id !== $user->id) {
            abort(403);
        }

        $group->update($request->validated());

        return new GroupResource($group);
    }

    #[Endpoint(title: 'Delete Group', description: 'Delete a specific group.')]
    #[Response(content: ['message' => 'This action is unauthorized.'], status: 403)]
    #[Response(content: ['message' => 'Record not found.'], status: 404)]
    #[Response(status: 204)]
    public function destroy(#[CurrentUser] User $user, Group $group)
    {
        if ($group->user_id !== $user->id) {
            abort(403);
        }

        $group->delete();

        return response()->noContent();
    }
}
