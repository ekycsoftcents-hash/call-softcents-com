<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Pages;

use App\Filament\Admin\Resources\Customers\Actions\ApprovalAction;
use App\Filament\Admin\Resources\Customers\Actions\DepositAction;
use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Filament\Admin\Resources\Customers\Schemas\CustomerInfolist;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use STS\FilamentImpersonate\Actions\Impersonate;

final class ViewCustomer extends ViewRecord
{
    protected static string $resource = CustomerResource::class;

    public function infolist(Schema $schema): Schema
    {
        return CustomerInfolist::configure($schema);
    }

    protected function getHeaderActions(): array
    {
        return [
            Impersonate::make()
                ->label('Login as Customer')
                ->requiresConfirmation()
                ->color('warning'),
            ApprovalAction::make()
                ->outlined(false),
            DepositAction::make(),
            EditAction::make(),
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Load relationship counts for statistics section
        /** @var User $record */
        $record = $this->getRecord();

        $data['campaigns_count'] = $record->campaigns()->count();
        $data['calls_count'] = $record->calls()->count();
        $data['audio_count'] = $record->audio()->count();
        $data['groups_count'] = $record->groups()->count();
        $data['templates_count'] = $record->templates()->count();
        $data['transactions_count'] = $record->transactions()->count();

        return $data;
    }
}
