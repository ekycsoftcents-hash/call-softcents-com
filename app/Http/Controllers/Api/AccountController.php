<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Account\ChangePasswordRequest;
use App\Http\Requests\Account\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Account', 'Manage user account')]
#[Authenticated]
#[ResponseFromApiResource(name: UserResource::class, model: User::class)]
#[Response(content: ['message' => 'Unauthenticated.'], status: 401)]
final class AccountController extends Controller
{
    #[Endpoint(title: 'Get user details', description: 'Retrieve details of the currently authenticated user.')]
    public function index(#[CurrentUser] User $user): JsonResource
    {
        return new UserResource($user);
    }

    #[Endpoint(
        title: 'Update profile',
        description: "Update the authenticated user's profile information."
    )]
    #[Response(
        content: [
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => ['The name field is required.'],
                'email' => ['The email has already been taken.'],
                'phone' => ['The phone must be a valid phone number.', 'The phone field format is invalid.'],
            ],
        ],
        status: 422
    )]
    public function update(#[CurrentUser] User $user, UpdateProfileRequest $request): JsonResource
    {
        $user->update($request->validated());

        if ($user->wasChanged('email')) {
            $user->update(['email_verified_at' => null]);
            $user->sendEmailVerificationNotification();
        }

        return new UserResource($user);
    }

    #[Endpoint(title: 'Change password', description: "Change the authenticated user's password.")]
    #[Response(
        content: [
            'message' => 'The given data was invalid.',
            'errors' => [
                'current_password' => ['The current password is incorrect.'],
                'password' => ['The password must be at least 8 characters.'],
            ],
        ],
        status: 422
    )]
    public function changePassword(#[CurrentUser] User $user, ChangePasswordRequest $request): JsonResponse|JsonResource
    {
        $user->update([
            'password' => $request->validated('password'),
        ]);

        return new UserResource($user);
    }
}
