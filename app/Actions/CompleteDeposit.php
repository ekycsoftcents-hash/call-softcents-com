<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\DepositStatus;
use App\Models\Deposit;
use App\Support\Payment\Data\PaymentResponse;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;
use Throwable;

final readonly class CompleteDeposit
{
    public function __construct(
        private CreditBalance $creditBalance,
    ) {}

    /**
     * Returns true if the deposit was completed by this call,
     * false if already completed or not applicable.
     *
     * @throws Throwable
     */
    public function handle(string $gateway, PaymentResponse $response): bool
    {
        return DB::transaction(function () use ($gateway, $response) {
            $deposit = Deposit::query()
                ->wherePaymentMethod($gateway)
                ->whereTransactionId($response->gatewayPaymentId)
                ->lockForUpdate()
                ->first();

            if (! $deposit) {
                return false;
            }

            if ($deposit->status === DepositStatus::Completed) {
                return false;
            }

            if (! $response->successful) {
                return false;
            }

            $deposit->update([
                'status' => DepositStatus::Completed,
                'paid_at' => now(),
                'meta' => $response->raw,
            ]);

            $this->creditBalance->handle(
                user: $deposit->user,
                amount: $deposit->amount,
                transactionable: $deposit,
                description: "Deposit of {$deposit->amount} via {$deposit->payment_method->value}"
            );

            DB::afterCommit(function () use ($deposit) {
                Notification::make()
                    ->icon(Heroicon::OutlinedCurrencyBangladeshi)
                    ->title('Deposit Completed')
                    ->body("Your deposit of {$deposit->amount} has been completed.")
                    ->sendToDatabase($deposit->user);
            });

            return true;
        }, attempts: 3);
    }
}
