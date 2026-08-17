<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Campaigns\Schemas;

use App\Enums\AudioApproval;
use App\Enums\AudioConversionStatus;
use App\Models\Caller;
use App\Models\Group as GroupModel;
use App\Rules\EnsureUserHasSufficientBalanceForCampaign;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use LaraZeus\Tabler\Tabler;

final class CampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(['lg' => 2])
                    ->schema([
                        Section::make('Campaign Details')
                            ->icon(Tabler::Ad2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Campaign Title')
                                    ->placeholder('Enter campaign title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull()
                                    ->prefixIcon(Tabler::AlphabetLatin),

                                Textarea::make('description')
                                    ->label('Campaign Description')
                                    ->placeholder('Enter campaign description')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Select::make('audio_id')
                                    ->prefixIcon(Tabler::Music)
                                    ->label('Audio')
                                    ->relationship(
                                        name: 'audio',
                                        titleAttribute: 'title',
                                        modifyQueryUsing: function (Builder $query) {
                                            return $query->where('approval', AudioApproval::Approved)
                                                ->where('conversion_status', AudioConversionStatus::Completed);
                                        })
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->rules(function (Get $get) {
                                        return [
                                            Rule::exists('audio', 'id')
                                                ->where('approval', AudioApproval::Approved)
                                                ->where('conversion_status', AudioConversionStatus::Completed)
                                                ->where('user_id', auth()->id()),
                                            new EnsureUserHasSufficientBalanceForCampaign($get('group_id')),
                                        ];
                                    }),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(['lg' => 1])
                    ->schema([
                        Section::make('Settings')
                            ->icon(Tabler::Settings)
                            ->schema([
                                Select::make('caller_id')
                                    ->label('Caller ID')
                                    ->relationship(
                                        name: 'caller',
                                        titleAttribute: 'caller_number',
                                        modifyQueryUsing: function (Builder $query): Builder {
                                            return $query->scopes(['enabled'])
                                                ->whereHas('users', function (Builder $q) {
                                                    $q->where('id', Auth::id());
                                                });
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Caller $record) => $record->name)
                                    ->preload()
                                    ->searchable(['caller_name', 'caller_number'])
                                    ->native(false)
                                    ->selectablePlaceholder(false)
                                    ->required(),
                                Select::make('group_id')
                                    ->prefixIcon(Tabler::AddressBook)
                                    ->label('Contact List')
                                    ->relationship(
                                        name: 'group',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function ($query) {
                                            return $query->whereHas('contacts');
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(function (GroupModel $record) {
                                        $record->loadCount('contacts');

                                        return $record->name.' ('.trans_choice(':count contact|:count contacts', $record->contacts_count).')';
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live(onBlur: true)
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Group Name')
                                            ->required()
                                            ->maxLength(255),

                                    ]),

                                DateTimePicker::make('scheduled_at')
                                    ->prefixIcon(Tabler::Calendar)
                                    ->label('Launch Date')
                                    ->placeholder('Select launch date and time')
                                    ->helperText('Leave empty to launch immediately')
                                    ->native(false)
                                    ->minDate(now()->addMinutes(5))
                                    ->maxDate(now()->addMonth()),
                            ]),
                    ]),
            ]);
    }
}
