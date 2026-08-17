<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Audience\Contacts\Schemas;

use App\Filament\Admin\Resources\Audience\Groups\GroupResource;
use App\Models\Contact;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Contact Overview Section
                Section::make('Contact Overview')
                    ->description('Essential contact information')
                    ->icon(Tabler::UserCircle)
                    ->schema([
                        TextEntry::make('phone_number')
                            ->label('Phone Number')
                            ->icon(Tabler::Phone)
                            ->copyable()
                            ->copyMessage('Phone number copied!')
                            ->copyMessageDuration(1500)
                            ->url(fn (Contact $record) => 'tel:'.$record->phone_number),
                    ])
                    ->columns(1)
                    ->collapsible(),

                // Group Association Section
                Section::make('Group Association')
                    ->description('Which group this contact belongs to')
                    ->icon(Tabler::AddressBook)
                    ->schema([
                        TextEntry::make('group.name')
                            ->label('Group')
                            ->icon(Tabler::Book)
                            ->badge()
                            ->color('info')
                            ->url(fn (Contact $record) => $record->group_id ? GroupResource::getUrl('view', ['record' => $record->group_id]) : null),
                        TextEntry::make('group.user.name')
                            ->label('Owner')
                            ->icon(Tabler::UserCheck)
                            ->placeholder('No owner'),
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
                            ->tooltip(fn (Contact $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (Contact $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                    ])
                    ->columns()
                    ->collapsed()
                    ->collapsible(),
            ]);
    }
}
