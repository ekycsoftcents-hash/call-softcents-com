<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Finance\Deposits\Pages;

use App\Enums\DepositStatus;
use App\Enums\TransactionType;
use App\Filament\Admin\Resources\Finance\Deposits\DepositResource;
use App\Models\Deposit;
use App\Models\Transaction;
use Filament\Resources\Pages\CreateRecord;

final class CreateDeposit extends CreateRecord
{
    protected static string $resource = DepositResource::class;

    protected function afterCreate(): void
    {
        /** @var Deposit $deposit */
        $deposit = $this->record;

        if ($deposit->status === DepositStatus::Completed) {
            $deposit->user->increment('balance', $deposit->amount);

            Transaction::create([
                'user_id' => $deposit->user_id,
                'type' => TransactionType::Credit,
                'amount' => $deposit->amount,
                'currency' => $deposit->currency,
                'description' => 'Deposit via '.ucfirst($deposit->gateway),
                'transactionable_type' => Deposit::class,
                'transactionable_id' => $deposit->id,
            ]);
        }
    }
}
