<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\DepositStatus;
use App\Filament\User\Resources\Finance\Deposits\DepositResource;
use App\Models\Deposit;
use Exception;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\Request;

final class PaymentController extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke(Request $request, string $gateway, Deposit $deposit)
    {
        abort_if($deposit->payment_method->value !== $gateway, 404);
        abort_if($deposit->user_id !== auth()->id(), 404);

        $notification = match ($deposit->status) {
            DepositStatus::Pending => [
                'title' => 'Payment Processing',
                'body' => 'Your payment is currently being processed.',
                'icon' => Heroicon::OutlinedClock,
                'color' => 'primary',
            ],
            DepositStatus::Completed => [
                'title' => 'Payment Completed',
                'body' => 'Your payment has been successfully processed.',
                'icon' => Heroicon::OutlinedCheckCircle,
                'color' => 'success',
            ],
            DepositStatus::Failed => [
                'title' => 'Payment Failed',
                'body' => 'Your payment could not be processed.',
                'icon' => Heroicon::OutlinedXCircle,
                'color' => 'danger',
            ],
            DepositStatus::Refunded => [
                'title' => 'Payment Refunded',
                'body' => 'This payment has been refunded.',
                'icon' => Heroicon::OutlinedArrowUturnLeft,
                'color' => 'warning',
            ],
        };

        Notification::make()
            ->title($notification['title'])
            ->body($notification['body'])
            ->icon($notification['icon'])
            ->color($notification['color'])
            ->send();

        return redirect(DepositResource::getUrl('view', [
            'record' => $deposit,
        ], panel: 'user'));
    }
}
