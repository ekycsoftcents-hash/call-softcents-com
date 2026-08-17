<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Groups\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class GroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Group Overview')
                    ->description('Essential group information')
                    ->icon(Tabler::AddressBook)
                    ->schema([
                        Group::make()
                            ->columns(4)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Group Name')
                                    ->icon(Tabler::Book),
                                TextEntry::make('contacts_count')
                                    ->label('Total Contacts')
                                    ->icon(Tabler::AddressBook)
                                    ->counts('contacts')
                                    ->numeric()
                                    ->default(0)
                                    ->badge()
                                    ->color('info')
                                    ->suffix(' contacts'),
                                TextEntry::make('created_at')
                                    ->label('Created')
                                    ->icon(Tabler::CalendarPlus)
                                    ->since()
                                    ->color('gray'),
                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->icon(Tabler::Refresh)
                                    ->since()
                                    ->color('gray'),
                            ]),

                        TextEntry::make('description')
                            ->label('Description')
                            ->markdown()
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->columns()
                    ->collapsible(),
            ]);
    }
}
