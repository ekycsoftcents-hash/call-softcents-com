<?php

declare(strict_types=1);

namespace App\Filament\User\Resources\Audience\Contacts;

use App\Enums\UserNavigationGroup;
use App\Filament\User\Resources\Audience\Contacts\Pages\CreateContact;
use App\Filament\User\Resources\Audience\Contacts\Pages\EditContact;
use App\Filament\User\Resources\Audience\Contacts\Pages\ListContacts;
use App\Filament\User\Resources\Audience\Contacts\Pages\ViewContact;
use App\Filament\User\Resources\Audience\Contacts\Schemas\ContactForm;
use App\Filament\User\Resources\Audience\Contacts\Schemas\ContactInfolist;
use App\Filament\User\Resources\Audience\Contacts\Tables\ContactsTable;
use App\Models\Contact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

final class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::AddressBook;

    protected static string|UnitEnum|null $navigationGroup = UserNavigationGroup::Audience;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ContactForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ContactInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ContactsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'create' => CreateContact::route('/create'),
            'view' => ViewContact::route('/{record}'),
            'edit' => EditContact::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('group', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }
}
