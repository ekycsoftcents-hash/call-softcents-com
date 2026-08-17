<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Schemas;

use App\Enums\UserAudioType;
use App\Models\Caller;
use App\Settings\CallingSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use LaraZeus\Tabler\Tabler;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->heading('Customer Information')
                    ->description('Basic information about the customer.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->prefixIcon(Tabler::User)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->prefixIcon(Tabler::Mail)
                            ->required()
                            ->maxLength(255),
                        PhoneInput::make('phone')
                            ->label('Phone')
                            ->required()
                            ->defaultCountry('BD')
                            ->rules('phone'),
                        TextInput::make('password')
                            ->label('Password')
                            ->hint('Leave empty to keep the current password.')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->revealable()
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->prefixAction(Action::make('generate_password')
                                ->label('Generate')
                                ->iconButton()
                                ->icon(Heroicon::ArrowPath)
                                ->action(function (TextInput $component) {
                                    $component->state(bin2hex(random_bytes(4)));
                                })
                            ),
                    ]),
                Tabs::make('Tabs')
                    ->tabs([
                        Tab::make('Calling')
                            ->schema([
                                Select::make('callers')
                                    ->prefixIcon(Tabler::IdBadge)
                                    ->label('Callers')
                                    ->relationship('callers')
                                    ->searchable(['caller_name', 'caller_number'])
                                    ->getOptionLabelFromRecordUsing(fn (Caller $record): string => $record->name)
                                    ->multiple()
                                    ->preload(),
                                Select::make('audio_type')
                                    ->prefixIcon(Tabler::MusicBolt)
                                    ->label('Audio Type')
                                    ->options(UserAudioType::class)
                                    ->default('both')
                                    ->required()
                                    ->native(false)
                                    ->selectablePlaceholder(false),
                                TextInput::make('pulse_rate')
                                    ->label('Pulse Rate')
                                    ->prefix('BDT')
                                    ->numeric()
                                    ->minValue(0)
                                    ->default(app(CallingSetting::class)->pulse_rate)
                                    ->required(),
                                TextInput::make('pulse_duration')
                                    ->prefixIcon(Tabler::ClockRecord)
                                    ->label('Pulse Duration')
                                    ->suffix('seconds')
                                    ->numeric()
                                    ->minValue(1)
                                    ->step(1)
                                    ->default(app(CallingSetting::class)->pulse_duration)
                                    ->required(),
                            ]),
                        Tab::make('Approval')
                            ->schema([
                                Toggle::make('auto_approve_audio')
                                    ->label('Auto Approve Audio')
                                    ->default(false)
                                    ->required(),
                                Toggle::make('auto_approve_campaigns')
                                    ->label('Auto Approve Campaigns')
                                    ->default(false)
                                    ->required(),
                            ]),
                    ])
                    ->persistTab()
                    ->id('customer_form_tabs'),
            ]);
    }
}
