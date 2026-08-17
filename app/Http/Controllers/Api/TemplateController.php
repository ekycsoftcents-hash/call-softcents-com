<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Template\StoreTemplateRequest;
use App\Http\Requests\Template\UpdateTemplateRequest;
use App\Http\Resources\TemplateResource;
use App\Models\Template;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response as ResponseAttribute;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Templates', 'Manage message templates')]
#[Authenticated]
final class TemplateController extends Controller
{
    /**
     * List Templates
     *
     * Get a list of all templates belonging to the authenticated user.
     */
    #[Endpoint(title: 'List Templates', description: 'Get a list of all templates belonging to the authenticated user.')]
    #[ResponseFromApiResource(name: TemplateResource::class, model: Template::class, collection: true, paginate: 15)]
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number.', required: false)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'Number of items per page.', required: false)]
    public function index(#[CurrentUser] User $user): AnonymousResourceCollection
    {
        $templates = $user->templates()->latest()->paginate();

        return TemplateResource::collection($templates);
    }

    /**
     * Create Template
     *
     * Create a new template.
     */
    #[Endpoint(title: 'Create Template', description: 'Create a new template.')]
    #[ResponseFromApiResource(name: TemplateResource::class, model: Template::class, status: 201)]
    #[ResponseAttribute(content: ['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]], status: 422)]
    public function store(#[CurrentUser] User $user, StoreTemplateRequest $request): TemplateResource
    {
        $template = $user->templates()->create($request->validated());

        return new TemplateResource($template);
    }

    /**
     * Get Template
     *
     * Get details of a specific template.
     */
    #[Endpoint(title: 'Get Template', description: 'Get details of a specific template.')]
    #[ResponseFromApiResource(name: TemplateResource::class, model: Template::class)]
    #[ResponseAttribute(content: ['message' => 'This action is unauthorized.'], status: 403)]
    #[ResponseAttribute(content: ['message' => 'Record not found.'], status: 404)]
    public function show(#[CurrentUser] User $user, Template $template): TemplateResource
    {
        if ($template->user_id !== $user->id) {
            abort(403);
        }

        return new TemplateResource($template);
    }

    /**
     * Update Template
     *
     * Update an existing template.
     */
    #[Endpoint(title: 'Update Template', description: 'Update an existing template.')]
    #[ResponseFromApiResource(name: TemplateResource::class, model: Template::class)]
    #[ResponseAttribute(content: ['message' => 'This action is unauthorized.'], status: 403)]
    #[ResponseAttribute(content: ['message' => 'Record not found.'], status: 404)]
    #[ResponseAttribute(content: ['message' => 'The given data was invalid.', 'errors' => ['name' => ['The name field is required.']]], status: 422)]
    public function update(#[CurrentUser] User $user, UpdateTemplateRequest $request, Template $template): TemplateResource
    {
        if ($template->user_id !== $user->id) {
            abort(403);
        }

        $template->update($request->validated());

        return new TemplateResource($template);
    }

    /**
     * Delete Template
     *
     * Delete a template.
     */
    #[Endpoint(title: 'Delete Template', description: 'Delete a template.')]
    #[ResponseAttribute(content: ['message' => 'This action is unauthorized.'], status: 403)]
    #[ResponseAttribute(content: ['message' => 'Record not found.'], status: 404)]
    #[ResponseAttribute(status: 204)]
    public function destroy(#[CurrentUser] User $user, Template $template): Response
    {
        if ($template->user_id !== $user->id) {
            abort(403);
        }

        $template->delete();

        return response()->noContent();
    }
}
