<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;

#[Authenticated]
#[Group('Account', 'Manage user account')]
#[Response(content: ['message' => 'Unauthenticated.'], status: 401)]
final class BalanceController extends Controller
{
    #[Endpoint(title: 'Get balance', description: "Retrieve the authenticated user's current balance.")]
    #[Response(content: ['data' => [
        'balance' => 100.50,
        'pulse_rate' => 0.45,
        'pulse_duration' => 30,
    ]])]
    public function show(#[CurrentUser] User $user): JsonResponse
    {
        return response()->json([
            'data' => [
                'balance' => $user->balance,
                'pulse_rate' => $user->pulse_rate,
                'pulse_duration' => $user->pulse_duration,
            ],
        ]);
    }
}
