<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('group.name')
                            ->icon(Tabler::AddressBook)
                            ->label('Group'),
                        TextEntry::make('name')
                            ->icon(Tabler::User)
                            ->label('Name')
                            ->placeholder('-'),
                        TextEntry::make('phone_number')
                            ->icon(Tabler::Phone)
                            ->label('Phone Number')
                            ->placeholder('-'),
                    ]),
                Section::make()
                    ->schema([
                        TextEntry::make('created_at')
                            ->icon(Tabler::CalendarPlus)
                            ->label('Created At')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->icon(Tabler::CalendarUp)
                            ->label('Last Updated')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
