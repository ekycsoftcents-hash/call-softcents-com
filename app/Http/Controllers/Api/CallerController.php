<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CallerResource;
use App\Models\Caller;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Authenticated]
#[Group(name: 'Callers', description: 'Manage callers')]
#[Response(content: ['message' => 'Unauthenticated.'], status: 401)]
final class CallerController extends Controller
{
    #[Endpoint(title: 'List Callers', description: 'Get a list of all callers belonging to the authenticated user.')]
    #[QueryParam(name: 'page', type: 'integer', description: 'The page number.', required: false)]
    #[QueryParam(name: 'per_page', type: 'integer', description: 'Number of items per page.', required: false)]
    #[ResponseFromApiResource(name: CallerResource::class, model: Caller::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user)
    {
        $callers = $user
            ->callers()
            ->enabled()
            ->latest()
            ->paginate();

        return CallerResource::collection($callers);
    }
}
