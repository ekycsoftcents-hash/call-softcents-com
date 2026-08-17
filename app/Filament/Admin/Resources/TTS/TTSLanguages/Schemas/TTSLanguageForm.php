<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\TTS\TTSLanguages\Schemas;

use App\Enums\TTSEngine;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

final class TTSLanguageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Language Name')
                            ->required(),
                        TextInput::make('code')
                            ->label('Language Code')
                            ->required()
                            ->unique(
                                table: 'tts_languages',
                                column: 'code',
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('engine', $get('engine')),
                            ),
                        Select::make('engine')
                            ->label('Engine')
                            ->options(TTSEngine::class)
                            ->default('azure')
                            ->searchable()
                            ->selectablePlaceholder(false)
                            ->required(),
                        Toggle::make('enabled')
                            ->label('Enabled')
                            ->required(),
                    ]),
            ]);
    }
}
