<?php

declare(strict_types=1);

namespace App\Filament\Admin\Pages;

use App\Enums\AdminNavigationGroup;
use App\Settings\CallingSetting;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

final class CallingSettings extends SettingsPage
{
    protected static string|UnitEnum|null $navigationGroup = AdminNavigationGroup::Settings;

    protected static string $settings = CallingSetting::class;

    protected static ?string $title = 'Calling';

    protected static ?int $navigationSort = 1;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('pulse_rate')
                            ->label('Pulse Rate')
                            ->prefix('BDT')
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->required()
                            ->helperText('The cost of each pulse in Bangladeshi Taka (BDT).'),
                        TextInput::make('pulse_duration')
                            ->label('Pulse Duration')
                            ->suffix('seconds')
                            ->numeric()
                            ->minValue(1)
                            ->step(1)
                            ->required()
                            ->helperText('The duration of each pulse in seconds.'),
                        TextInput::make('max_retry_attempts')
                            ->label('Max Retry Attempts')
                            ->suffix('times')
                            ->numeric()
                            ->minValue(0)
                            ->step(1)
                            ->required()
                            ->helperText('The maximum number of retry attempts for failed calls.'),
                        TextInput::make('campaign_success_threshold')
                            ->label('Campaign Success Threshold')
                            ->suffix('%')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.01)
                            ->required()
                            ->helperText('The percentage of successful calls required for a campaign to be considered successful.'),
                    ]),
            ]);
    }
}
