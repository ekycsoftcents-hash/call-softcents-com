<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Callers\Schemas;

use App\Enums\UserType;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

final class CallerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('server_id')
                            ->relationship('server', 'name')
                            ->label('Server')
                            ->required()
                            ->searchable()
                            ->selectablePlaceholder(false)
                            ->preload(),
                        TextInput::make('trunk_name')
                            ->label('Trunk Name')
                            ->helperText('Name of the outbound gateway configured in FusionPBX')
                            ->required(),
                        TextInput::make('sip_username')
                            ->label('Client SIP Username')
                            ->required(),
                        TextInput::make('sip_password')
                            ->label('Client SIP Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create'),
                        TextInput::make('sip_domain')
                            ->label('SIP Domain')
                            ->required(),
                        TextInput::make('sip_port')
                            ->label('SIP Port')
                            ->numeric()
                            ->default(5060),
                        TextInput::make('sip_context')
                            ->label('SIP Context')
                            ->default('from-internal')
                            ->required(),
                        TextInput::make('max_concurrency')
                            ->label('Max Concurrency')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Maximum number of concurrent calls this caller ID can handle.'),
                        Select::make('enabled')
                            ->label('Enabled')
                            ->boolean()
                            ->required(),
                    ]),
                Section::make()
                    ->schema([
                        TextInput::make('caller_name')
                            ->label('Caller Name')
                            ->required(),
                        TextInput::make('caller_number')
                            ->label('Caller Number')
                            ->required(),
                        Select::make('users')
                            ->relationship('users', 'name', modifyQueryUsing: function (Builder $query) {
                                return $query->where('type', UserType::User);
                            })
                            ->label('Assigned Users')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }
}
