<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Groups\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make()
                    ->heading('Group Details')
                    ->description('Enter the details of the group')
                    ->collapsible()
                    ->schema([
                        TextInput::make('name')
                            ->prefixIcon(Tabler::AddressBook)
                            ->label('Name')
                            ->placeholder('Enter group name')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Enter group description')
                            ->columnSpanFull(),
                    ]),
                Section::make()
                    ->heading('Contacts')
                    ->description('Add contacts to the group')
                    ->visibleOn(['create', 'createOption'])
                    ->schema([
                        Repeater::make('contacts')
                            ->hiddenLabel()
                            ->relationship()
                            ->defaultItems(0)
                            ->table([
                                TableColumn::make('Phone Number')
                                    ->markAsRequired(),
                            ])
                            ->schema([
                                PhoneInput::make('phone_number')
                                    ->label('Phone Number')
                                    ->placeholder('Enter phone number')
                                    ->defaultCountry('BD')
                                    ->required(),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
