<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\AudioType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Audio\StoreAudioRequest;
use App\Http\Requests\Audio\UpdateAudioRequest;
use App\Http\Resources\AudioResource;
use App\Models\Audio;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Authenticated]
#[Group(name: 'Audios', description: 'Manage audio files and TTS')]
#[Response(content: ['message' => 'This action is unauthorized.'], status: 403, description: 'Unauthorized')]
final class AudioController extends Controller
{
    #[Endpoint(title: 'List Audios', description: 'Retrieve a list of audios for the current user.')]
    #[QueryParam(name: 'type', type: 'enum', description: 'Filter by type', required: false, enum: AudioType::class)]
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number.', required: false)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'Number of items per page.', required: false)]
    #[ResponseFromApiResource(name: AudioResource::class, model: User::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user, Request $request): ResourceCollection
    {
        $audios = Audio::whereUserId($user->id)
            ->with(['ttsArtist'])
            ->when($request->has('type'), function ($query) use ($request) {
                $query->where('type', $request->input('type'));
            })
            ->latest()
            ->paginate();

        return AudioResource::collection($audios);
    }

    #[Endpoint(title: 'Create Audio', description: 'Create a new audio (upload or TTS).')]
    #[ResponseFromApiResource(name: AudioResource::class, model: Audio::class, status: 201)]
    #[Response(content: ['message' => 'The given data was invalid.', 'errors' => ['type' => ['The type field is required.']]], status: 422)]
    public function store(#[CurrentUser] User $user, StoreAudioRequest $request)
    {
        $data = $request->validated();

        if (AudioType::tryFrom($request->type) === AudioType::Upload) {
            $file = $request->file('file');
            $path = $file->store('audios/originals', config('filesystems.default'));
            $data['original_path'] = $path;
            $data['size'] = $file->getSize();

            unset($data['file']);
        }

        /** @var Audio $audio */
        $audio = $user->audio()->create($data);

        return new AudioResource($audio);
    }

    #[Endpoint(title: 'Get Audio', description: 'Retrieve a specific audio.')]
    #[ResponseFromApiResource(name: AudioResource::class, model: Audio::class)]
    public function show(#[CurrentUser] User $user, Audio $audio)
    {
        if ($audio->user_id !== $user->id) {
            abort(403);
        }

        return new AudioResource($audio);
    }

    #[Endpoint(title: 'Update Audio', description: 'Update a specific audio.')]
    #[ResponseFromApiResource(name: AudioResource::class, model: Audio::class)]
    #[Response(content: ['message' => 'The given data was invalid.', 'errors' => ['title' => ['The title field is required.']]], status: 422)]
    #[Response(content: ['message' => 'Record not found.'], status: 404, description: 'Not Found')]
    public function update(#[CurrentUser] User $user, UpdateAudioRequest $request, Audio $audio)
    {
        if ($audio->user_id !== $user->id) {
            abort(403);
        }

        $audio->update($request->validated());

        return new AudioResource($audio);
    }

    #[Endpoint(title: 'Delete Audio', description: 'Delete a specific audio.')]
    #[Response(status: 204)]
    public function destroy(#[CurrentUser] User $user, Audio $audio)
    {
        if ($audio->user_id !== $user->id) {
            abort(403);
        }

        $audio->delete();

        return response()->noContent();
    }
}
