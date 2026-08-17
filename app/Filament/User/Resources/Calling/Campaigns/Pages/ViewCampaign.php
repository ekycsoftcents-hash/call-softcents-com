<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Pages;

use App\Enums\CampaignStatus;
use App\Enums\TransactionType;
use App\Filament\User\Resources\Calling\Campaigns\CampaignResource;
use App\Filament\User\Resources\Calling\Campaigns\Widgets\CampaignChartWidget;
use App\Filament\User\Resources\Calling\Campaigns\Widgets\CampaignDurationChartWidget;
use App\Filament\User\Resources\Calling\Campaigns\Widgets\CampaignStatsWidget;
use App\Models\Campaign;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\DB;
use LaraZeus\Tabler\Tabler;

final class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->getRetryAction(),
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CampaignStatsWidget::class,
            CampaignChartWidget::class,
            CampaignDurationChartWidget::class,
        ];
    }

    protected function getRetryAction()
    {
        return Action::make('retryFailed')
            ->icon(Tabler::RefreshDot)
            ->label('Retry Failed')
            ->color('warning')
            ->outlined()
            ->requiresConfirmation()
            ->modalDescription(function (Campaign $record): string {
                $failedCount = $record->calls()->retryable()->count();

                if ($failedCount === 0) {
                    return 'There are no failed calls to retry.';
                }

                $costPerCall = $record->audio->cost;
                $totalCost = $failedCount * $costPerCall;

                return "This will retry all {$failedCount} failed calls in this campaign at a total cost of {$totalCost} BDT. Do you want to proceed?";
            })
            ->visible(fn (Campaign $record): bool => $record->calls()->retryable()->exists())
            ->action(function (Campaign $record): void {
                DB::transaction(function () use ($record) {
                    $lockedUser = User::lockForUpdate()->findOrFail($record->user_id);

                    $query = $record->calls()->retryable();

                    $count = (clone $query)->count();

                    if ($count === 0) {
                        return;
                    }

                    $costPerCall = $record->audio->cost;
                    $totalCost = $count * $costPerCall;

                    if (! $lockedUser->hasEnoughBalance($totalCost)) {
                        Notification::make()
                            ->title('Insufficient Balance')
                            ->body('You do not have enough balance to retry all failed calls in this campaign.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $query->update([
                        'status' => 'pending',
                        'cost' => $costPerCall,
                        'retries' => DB::raw('retries + 1'),
                        'updated_at' => now(),
                    ]);

                    $record->transactions()->create([
                        'user_id' => $lockedUser->id,
                        'type' => TransactionType::Debit,
                        'amount' => $totalCost,
                        'balance_before' => $lockedUser->balance,
                        'balance_after' => $lockedUser->balance - $totalCost,
                        'currency' => 'BDT',
                        'description' => "Reserved balance for retrying failed calls in campaign #{$record->id}",
                    ]);

                    $lockedUser->decrement('balance', $totalCost);

                    if (in_array($record->status, [CampaignStatus::Finished, CampaignStatus::Failed])) {
                        $record->update(['status' => CampaignStatus::Processing]);
                    }
                });

                $this->refresh();
            });
    }
}
