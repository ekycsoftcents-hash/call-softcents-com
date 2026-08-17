<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\Calling\Campaigns;

use App\Enums\AdminNavigationGroup;
use App\Filament\Admin\Resources\Calling\Campaigns\Pages\ListCampaigns;
use App\Filament\Admin\Resources\Calling\Campaigns\Schemas\CampaignInfolist;
use App\Filament\Admin\Resources\Calling\Campaigns\Tables\CampaignsTable;
use App\Models\Campaign;
use App\Models\Scopes\OwnedByAuthUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\Tabler\Tabler;
use UnitEnum;

final class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static string|BackedEnum|null $navigationIcon = Tabler::Speakerphone;

    protected static string|null|UnitEnum $navigationGroup = AdminNavigationGroup::Calling;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function infolist(Schema $schema): Schema
    {
        return CampaignInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CampaignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\CallsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'view' => Pages\ViewCampaign::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                OwnedByAuthUser::class,
            ]);
    }
}
