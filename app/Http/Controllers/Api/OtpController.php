<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\CreateNewOtpCall;
use App\Enums\CallInterface;
use App\Enums\CallType;
use App\Exceptions\BusinessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Otp\StoreOtpRequest;
use App\Http\Resources\CallResource;
use App\Models\Call;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Throwable;

#[Group('OTP', 'Manage one-time password calls')]
#[Authenticated]
#[Response(status: 401, description: 'Unauthenticated')]
#[Response(status: 403, description: 'Unauthorized')]
final class OtpController extends Controller
{
    #[Endpoint(title: 'List OTP Calls')]
    #[ResponseFromApiResource(CallResource::class, Call::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user)
    {
        $opts = $user
            ->calls()
            ->where('type', CallType::OTP)
            ->latest()
            ->paginate();

        return CallResource::collection($opts);
    }

    #[Endpoint(title: 'Send OTP Call')]
    #[BodyParam(
        name: 'caller_id',
        type: 'integer',
        description: 'The ID of the caller to use for the OTP call. The caller must be enabled and belong to the authenticated user.',
        required: true,
        example: 10
    )]
    #[BodyParam(
        name: 'code',
        type: 'integer',
        description: 'The one-time password code to be sent in the call. Must be an integer with a maximum of 10 digits.',
        required: true,
        example: '123456'
    )]
    #[BodyParam(
        name: 'recipient',
        type: 'string',
        description: 'The phone number of the recipient who will receive the OTP call. Must be a string with a maximum length of 15 characters.',
        required: true,
        example: '+8801712345678'
    )]
    #[ResponseFromApiResource(CallResource::class, Call::class, status: 201)]
    public function store(#[CurrentUser] User $user, StoreOtpRequest $request)
    {
        try {
            $call = app(CreateNewOtpCall::class)->handle(
                $user,
                [
                    'phone_number' => $request->input('recipient'),
                    'otp' => $request->input('code'),
                    'caller_id' => $request->input('caller_id'),
                ],
                CallInterface::API,
            );

            return new CallResource($call->unsetRelation('user')->unsetRelation('audio'));
        } catch (BusinessException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['balance' => [$e->getMessage()]],
            ], 422);
        } catch (Throwable) {
            return response()->json([
                'message' => 'OTP Call Creation Failed',
                'errors' => ['general' => ['Something went wrong while creating the OTP call. Please try again.']],
            ], 500);
        }
    }
}
