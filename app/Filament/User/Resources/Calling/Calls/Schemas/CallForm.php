<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Calling\Calls\Schemas;

use App\Enums\AudioApproval;
use App\Enums\AudioConversionStatus;
use App\Models\Caller;
use App\Rules\EnsureUserHasSufficientBalanceForCall;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use LaraZeus\Tabler\Tabler;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

final class CallForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components(components: [
                Section::make()
                    ->schema(components: [
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
                            ->searchable(['caller_name', 'caller_number'])
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn (Caller $caller) => $caller->name),
                        PhoneInput::make('phone_number')
                            ->label('Phone Number')
                            ->onlyCountries(['BD'])
                            ->defaultCountry('BD')
                            ->required()
                            ->rules(['phone:BD']),
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
                            ->rules([
                                Rule::exists('audio', 'id')
                                    ->where('approval', AudioApproval::Approved)
                                    ->where('conversion_status', AudioConversionStatus::Completed)
                                    ->where('user_id', auth()->id()),
                                new EnsureUserHasSufficientBalanceForCall(),
                            ]),
                        DateTimePicker::make('scheduled_at')
                            ->label('Scheduled At')
                            ->prefixIcon(Tabler::CalendarTime)
                            ->minDate(now()->addMinutes(5))
                            ->maxDate(now()->addDays(30)),
                    ]),
            ]);
    }
}
