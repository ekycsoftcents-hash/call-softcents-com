<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns()
                    ->schema([
                        Select::make('group_id')
                            ->relationship('group', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(request()->input('group_id')),
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
}
