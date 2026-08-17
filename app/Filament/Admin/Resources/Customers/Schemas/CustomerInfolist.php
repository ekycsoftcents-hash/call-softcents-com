<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Customers\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use LaraZeus\Tabler\Tabler;

final class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Customer Overview Section
                Section::make('Customer Overview')
                    ->description('Essential customer information and account details')
                    ->icon(Tabler::UserCircle)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->icon(Tabler::User),
                        TextEntry::make('type')
                            ->label('Account Type')
                            ->icon(Tabler::ShieldCheck)
                            ->badge(),
                        TextEntry::make('email_verified_at')
                            ->label('Verification Status')
                            ->icon(Tabler::RosetteDiscountCheck)
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Unverified')
                            ->color(fn ($state) => $state ? 'success' : 'warning'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // Contact Information Section
                Section::make('Contact Information')
                    ->description('How to reach this customer')
                    ->icon(Tabler::Messages)
                    ->schema([
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon(Tabler::Mail)
                            ->copyable()
                            ->copyMessage('Email copied!')
                            ->copyMessageDuration(1500),
                        TextEntry::make('phone')
                            ->label('Phone Number')
                            ->icon(Tabler::Phone)
                            ->copyable()
                            ->copyMessage('Phone number copied!')
                            ->copyMessageDuration(1500)
                            ->placeholder('No phone number provided'),
                    ])
                    ->columns()
                    ->collapsible(),

                // Account & Billing Section
                Section::make('Account & Billing')
                    ->description('Financial and pulse configuration details')
                    ->icon(Tabler::Coin)
                    ->schema([
                        TextEntry::make('balance')
                            ->label('Current Balance')
                            ->icon(Tabler::Wallet)
                            ->money('BDT')
                            ->size('lg')
                            ->weight('bold')
                            ->color(fn ($state) => $state > 0 ? 'success' : ($state < 0 ? 'danger' : 'gray')),
                        TextEntry::make('pulse_rate')
                            ->label('Pulse Rate')
                            ->icon(Tabler::Activity)
                            ->numeric(decimalPlaces: 2)
                            ->suffix(' /min')
                            ->placeholder('Not configured'),
                        TextEntry::make('pulse_duration')
                            ->label('Pulse Duration')
                            ->icon(Tabler::Clock)
                            ->numeric()
                            ->suffix(' seconds')
                            ->placeholder('Not configured'),
                        TextEntry::make('audio_type')
                            ->label('Audio Type')
                            ->icon(Tabler::Volume)
                            ->badge()
                            ->placeholder('Not specified'),
                    ])
                    ->columns()
                    ->collapsible(),

                // Activity & Statistics
                Section::make('Activity & Statistics')
                    ->description('Customer engagement and usage metrics')
                    ->icon(Tabler::ChartBar)
                    ->schema([
                        TextEntry::make('campaigns_count')
                            ->label('Total Campaigns')
                            ->icon(Tabler::Speakerphone)
                            ->counts('campaigns')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('info'),
                        TextEntry::make('calls_count')
                            ->label('Total Calls')
                            ->icon(Tabler::PhoneOutgoing)
                            ->counts('calls')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('success'),
                        TextEntry::make('audio_count')
                            ->label('Audio Files')
                            ->icon(Tabler::Music)
                            ->counts('audio')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('purple'),
                        TextEntry::make('groups_count')
                            ->label('Groups')
                            ->icon(Tabler::AddressBook)
                            ->counts('groups')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('warning'),
                        TextEntry::make('templates_count')
                            ->label('Templates')
                            ->icon(Tabler::Files)
                            ->counts('templates')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('indigo'),
                        TextEntry::make('deposits_count')
                            ->label('Deposits')
                            ->icon(Tabler::Cash)
                            ->counts('deposits')
                            ->numeric()
                            ->default(0)
                            ->badge()
                            ->color('cyan'),
                    ])
                    ->columns(3)
                    ->collapsible(),

                // System Information Section
                Section::make('System Information')
                    ->description('Account timestamps and metadata')
                    ->icon(Tabler::InfoCircle)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Account Created')
                            ->icon(Tabler::CalendarPlus)
                            ->since()
                            ->tooltip(fn (User $record) => $record->created_at ? $record->created_at->format('M j, Y \a\t h:i A') : 'Unknown')
                            ->color('gray'),
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->icon(Tabler::Refresh)
                            ->since()
                            ->tooltip(fn (User $record) => $record->updated_at ? $record->updated_at->format('M j, Y \a\t h:i A') : 'Never updated')
                            ->color('gray'),
                        TextEntry::make('email_verified_at')
                            ->label('Email Verified At')
                            ->icon(Tabler::CircleCheck)
                            ->since()
                            ->tooltip(fn (User $record) => $record->email_verified_at ? $record->email_verified_at->format('M j, Y \a\t h:i A') : 'Not verified yet')
                            ->placeholder('Email not verified yet')
                            ->color('success'),
                        TextEntry::make('deleted_at')
                            ->label('Deleted At')
                            ->icon(Tabler::Trash)
                            ->since()
                            ->tooltip(fn (User $record) => $record->deleted_at ? $record->deleted_at->format('M j, Y \a\t h:i A') : 'Account is active')
                            ->placeholder('Account is active')
                            ->color('danger'),
                    ])
                    ->columns()
                    ->collapsible(),
                Section::make('Documents')
                    ->description('Uploaded documents and files')
                    ->icon(Tabler::Folder)
                    ->schema([
                        ImageEntry::make('front_nid')
                            ->label('Front NID')
                            ->placeholder('No front NID uploaded')
                            ->visibility('private'),
                        ImageEntry::make('back_nid')
                            ->label('Back NID')
                            ->placeholder('No back NID uploaded')
                            ->visibility('private'),
                    ])
                    ->columns()
                    ->collapsible(),
            ]);
    }
}
