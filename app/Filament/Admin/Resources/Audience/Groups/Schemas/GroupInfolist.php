<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Groups\Schemas;

use App\Filament\Admin\Resources\Customers\CustomerResource;
use App\Models\Group;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class GroupInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Group Overview Section
                Section::make('Group Overview')
                    ->description('Essential group information')
                    ->icon(Tabler::AddressBook)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Group Name')
                            ->icon(Tabler::Book),
                        TextEntry::make('user.name')
                            ->label('Owner')
                            ->icon(Tabler::UserCheck)
                            ->badge()
                            ->url(fn (Group $record) => $record->user_id ? CustomerResource::getUrl('view', ['record' => $record->user_id]) : null),
                    ])
                    ->columns()
                    ->collapsible(),

                // Description Section
                Section::make('Description')
                    ->description('Additional details about this group')
                    ->icon(Tabler::FileText)
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->markdown()
                            ->placeholder('No description provided')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Statistics Section
                Section::make('Statistics')
                    ->description('Group usage and contact metrics')
                    ->icon(Tabler::ChartBar)
                    ->schema([
                        TextEntry::make('contacts_count')
                            ->label('Total Contacts')
                            ->icon(Tabler::AddressBook)
                            ->counts('contacts')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('info')
                            ->suffix(' contacts'),
                    ])
                    ->columns()
                    ->collapsible(),

                // System Information Section
                Section::make('System Information')
                    ->description('Record timestamps and metadata')
                    ->icon(Tabler::InfoCircle)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created')
                            ->icon(Tabler::CalendarPlus)
                            ->since()
                            ->tooltip(fn (Group $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (Group $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                    ])
                    ->columns()
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
