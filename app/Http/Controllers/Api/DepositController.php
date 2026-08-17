<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\DepositStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Deposit\StoreDepositRequest;
use App\Http\Resources\DepositResource;
use App\Models\Deposit;
use App\Models\User;
use App\Support\Payment\PaymentService;
use Exception;
use Illuminate\Container\Attributes\CurrentUser;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;

#[Group('Deposits', 'Manage deposits')]
#[Authenticated]
final class DepositController extends Controller
{
    public function __construct(protected PaymentService $paymentService) {}

    #[Endpoint(title: 'List Deposits', description: 'Retrieve a list of deposits for the current user.')]
    #[ResponseFromApiResource(name: DepositResource::class, model: Deposit::class, collection: true, paginate: 15)]
    public function index(#[CurrentUser] User $user)
    {
        return DepositResource::collection($user->deposits()->latest()->paginate(15));
    }

    /**
     * @throws Exception
     */
    #[Endpoint(title: 'Initiate Deposit', description: 'Initiate a new deposit.')]
    #[ResponseFromApiResource(name: DepositResource::class, model: Deposit::class, status: 201)]
    #[Response(content: ['message' => 'The given data was invalid.', 'errors' => ['amount' => ['The amount field is required.']]], status: 422)]
    public function store(#[CurrentUser] User $user, StoreDepositRequest $request)
    {
        $deposit = $user->deposits()->create([
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'BDT',
            'gateway' => $request->gateway,
            'status' => 'pending',
        ]);

        try {
            $paymentData = $this->paymentService->initiate($deposit);

            $deposit->update([
                'transaction_id' => $paymentData['id'],
                'meta_data' => ['checkout_url' => $paymentData['url']],
            ]);

            return new DepositResource($deposit);
        } catch (Exception $e) {
            $deposit->update(['status' => 'failed']);
            throw $e;
        }
    }

    #[Endpoint(title: 'Get Deposit', description: 'Retrieve a specific deposit.')]
    #[ResponseFromApiResource(name: DepositResource::class, model: Deposit::class)]
    #[Response(content: ['message' => 'Record not found.'], status: 404)]
    public function show(#[CurrentUser] User $user, Deposit $deposit)
    {
        if ($deposit->user_id !== $user->id) {
            abort(403);
        }

        return new DepositResource($deposit);
    }

    #[Endpoint(title: 'Verify Deposit', description: 'Verify a deposit status.')]
    #[ResponseFromApiResource(name: DepositResource::class, model: Deposit::class)]
    public function verify(#[CurrentUser] User $user, Deposit $deposit)
    {
        if ($deposit->user_id !== $user->id) {
            abort(403);
        }

        if ($deposit->status === DepositStatus::Completed) {
            return new DepositResource($deposit);
        }

        $verified = $this->paymentService->verify($deposit);

        if ($verified) {
            $this->paymentService->confirm($deposit);
        }

        return new DepositResource($deposit);
    }
}
