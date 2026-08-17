<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Settings\Servers;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\Settings\Servers\Pages\CreateServer;
use App\Filament\Admin\Resources\Settings\Servers\Pages\EditServer;
use App\Filament\Admin\Resources\Settings\Servers\Pages\ListServers;
use App\Filament\Admin\Resources\Settings\Servers\Schemas\ServerForm;
use App\Filament\Admin\Resources\Settings\Servers\Tables\ServersTable;
use App\Models\Server;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

final class ServerResource extends Resource
{
    protected static ?string $model = Server::class;

    protected static string|null|UnitEnum $navigationGroup = AdminNavigationGroup::Settings;

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ServerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServersTable::configure($table);
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
            'index' => ListServers::route('/'),
            'create' => CreateServer::route('/create'),
            'edit' => EditServer::route('/{record}/edit'),
        ];
    }
}
