<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\RelationManagers;

use App\Enums\TTSArtistGender;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Collection;

final class ArtistsRelationManager extends RelationManager
{
    protected static string $relationship = 'ttsArtists';

    protected static ?string $title = 'TTS Artists';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Artist Name')
                            ->required(),
                        Select::make('gender')
                            ->label('Gender')
                            ->options(TTSArtistGender::class)
                            ->required(),
                        TextInput::make('code')
                            ->label('Artist Code')
                            ->required(),
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->required(),
                    ]),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {

        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Artist Name')
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->badge(),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable(),
                IconColumn::make('enabled')
                    ->label('Enabled')
                    ->alignCenter()
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('New artist')
                    ->modalWidth(Width::Medium),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalWidth(Width::Medium),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('enable')
                        ->label('Enable selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['enabled' => true]))
                        ->color('success')
                        ->icon(Heroicon::OutlinedCheckCircle)
                        ->successNotificationTitle('Selected artists have been enabled.'),
                    BulkAction::make('disable')
                        ->label('Disable selected')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['enabled' => false]))
                        ->color('danger')
                        ->icon(Heroicon::OutlinedXCircle)
                        ->successNotificationTitle('Selected artists have been disabled.'),
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
