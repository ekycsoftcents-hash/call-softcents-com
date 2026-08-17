<?php

namespace App\Filament\User\Resources\Finance\Deposits\Actions;

use App\Enums\DepositPaymentMethod;
use App\Enums\DepositStatus;
use App\Support\Payment\Data\PaymentRequest;
use App\Support\Payment\Data\PaymentResponse;
use App\Support\Payment\Payment;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class DepositAction
{
    public static function make()
    {
        return Action::make('deposit')
            ->icon(Heroicon::OutlinedCurrencyBangladeshi)
            ->label('Top Up')
            ->modalWidth(Width::Medium)
            ->modalIcon(Heroicon::CurrencyBangladeshi)
            ->modalHeading('Add Balance')
            ->modalDescription('Minimum deposit amount is BDT 50 and maximum deposit amount is BDT 100,000.')
            ->requiresConfirmation()
            ->schema([
                TextInput::make('amount')
                    ->prefix('BDT')
                    ->label('Amount')
                    ->placeholder('Enter amount to deposit')
                    ->required()
                    ->numeric()
                    ->minValue(50)
                    ->maxValue(100_000),
            ])
            ->action(function (array $data) {
                $response = DB::transaction(function () use ($data): PaymentResponse {
                    $user = auth()->user();
                    $gateway = 'piprapay';

                    $deposit = $user->deposits()->create([
                        'amount' => $data['amount'],
                        'currency' => 'BDT',
                        'payment_method' => DepositPaymentMethod::PipraPay,
                        'status' => DepositStatus::Pending,
                    ]);

                    $response = Payment::driver($gateway)
                        ->create(new PaymentRequest(
                            orderId: $deposit->id,
                            amount: $data['amount'],
                            currency: 'BDT',
                            customerName: $user->name,
                            customerEmail: $user->email,
                            customerPhone: $user->phone,
                            returnUrl: route('payments.callback', ['gateway' => $gateway, 'deposit' => $deposit->id]),
                            webhookUrl: route('payments.webhook', ['gateway' => $gateway]),
                            metadata: ['order_id' => $deposit->id]
                        ));

                    if ($response->successful) {
                        $deposit->update([
                            'transaction_id' => $response->gatewayPaymentId,
                            'meta' => $response->raw,
                        ]);

                        return $response;
                    }

                    throw new Exception("Failed to initiate payment: $response->errorMessage");
                });

                return redirect()->away($response->checkoutUrl);
            });
    }
}
