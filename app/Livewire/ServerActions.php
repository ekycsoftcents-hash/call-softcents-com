<?php

declare(strict_types=1);

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Process;
use Livewire\Component;

final class ServerActions extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function render(): View
    {
        return view('livewire.server-actions');
    }

    public function restartHorizonAction(): Action
    {
        return Action::make('restartHorizon')
            ->label('Restart Horizon')
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Restart Horizon?')
            ->modalDescription('This will gracefully terminate all Horizon workers and Supervisor will restart them automatically. Any jobs currently being processed will finish first, but the dashboard will briefly go offline. Continue?')
            ->modalSubmitActionLabel('Yes, restart')
            ->action(function () {
                Process::run('php artisan horizon:terminate');

                Notification::make()
                    ->title('Horizon is restarting')
                    ->body('Workers will be back online in a few seconds. Refresh the Horizon dashboard if it appears unavailable.')
                    ->success()
                    ->send();
            });
    }

    public function actionsGroup(): ActionGroup
    {
        return ActionGroup::make([
            $this->restartHorizonAction(),
        ])
            ->label('Server')
            ->size('md')
            ->color('gray')
            ->hiddenLabel(false)
            ->icon(Heroicon::OutlinedServer);
    }
}
