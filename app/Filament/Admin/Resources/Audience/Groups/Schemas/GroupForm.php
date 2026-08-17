<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

final class GroupForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('User')
                            ->searchable()
                            ->preload()
                            ->required(),
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
