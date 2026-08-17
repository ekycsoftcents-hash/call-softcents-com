<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\RelationManagers;

use App\Filament\Exports\ContactExporter;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\Tabler\Tabler;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        PhoneInput::make('phone_number')
                            ->label('Phone Number')
                            ->defaultCountry('BD')
                            ->onlyCountries(['BD'])
                            ->enableIpLookup(false)
                            ->required()
                            ->rules(['phone:BD']),
                    ]),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('phone_number')
                    ->label('Phone Number')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('phone_number')
            ->columns([
                TextColumn::make('phone_number')
                    ->label('Phone Number')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
                ExportAction::make('export')
                    ->label('Export')
                    ->icon(Tabler::TableExport)
                    ->exporter(ContactExporter::class)
                    ->columnMappingColumns(3),
            ]);
    }
}
