<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns\Schemas;

use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use App\Models\Campaign;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use LaraZeus\Tabler\Tabler;

final class CampaignInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make()
                            ->heading('Campaign Details')
                            ->icon(Tabler::Ad2)
                            ->schema([
                                TextEntry::make('title')
                                    ->label('Campaign Title')
                                    ->weight(FontWeight::Bold)
                                    ->size(TextSize::Large)
                                    ->columnSpanFull(),

                                TextEntry::make('description')
                                    ->markdown()
                                    ->placeholder('No description provided.')
                                    ->columnSpanFull(),

                                Grid::make([
                                    'default' => 1,
                                    'sm' => 2,
                                    'md' => 3,
                                ])
                                    ->schema([
                                        TextEntry::make('audio.title')
                                            ->label('Audio File')
                                            ->icon(Tabler::Music)
                                            ->limit(20),

                                        TextEntry::make('group.name')
                                            ->label('Group')
                                            ->icon(Tabler::AddressBook)
                                            ->placeholder('N/A')
                                            ->visible(fn (Campaign $record) => $record->group_id !== null)
                                            ->url(fn (Campaign $record) => GroupResource::getUrl('edit', ['record' => $record->group_id])),
                                        TextEntry::make('status')
                                            ->label('Current Status')
                                            ->badge(),
                                    ]),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make()
                            ->heading('Timestamps')
                            ->icon(Tabler::Clock)
                            ->collapsible()
                            ->schema([
                                TextEntry::make('scheduled_at')
                                    ->label('Launch Date')
                                    ->icon(Tabler::CalendarEvent)
                                    ->placeholder('Not Scheduled')
                                    ->dateTime(),

                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->icon(Tabler::CalendarPlus)
                                    ->dateTime()
                                    ->sinceTooltip(),

                                TextEntry::make('updated_at')
                                    ->label('Last Updated')
                                    ->icon(Tabler::CalendarUp)
                                    ->dateTime()
                                    ->sinceTooltip(),
                            ]),
                    ]),
            ]);
    }
}
