<?php

namespace App\Filament\Resources\PackageAddons;

use App\Filament\Resources\PackageAddons\Pages\CreatePackageAddon;
use App\Filament\Resources\PackageAddons\Pages\EditPackageAddon;
use App\Filament\Resources\PackageAddons\Pages\ListPackageAddons;
use App\Filament\Resources\PackageAddons\Schemas\PackageAddonForm;
use App\Filament\Resources\PackageAddons\Tables\PackageAddonsTable;
use App\Models\PackageAddon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

use UnitEnum;

class PackageAddonResource extends Resource
{
    protected static ?string $model = PackageAddon::class;

    protected static string | UnitEnum | null $navigationGroup = 'Packages';

    protected static ?string $navigationLabel = 'Package Add-ons';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PackageAddonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageAddonsTable::configure($table);
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
            'index' => ListPackageAddons::route('/'),
            'create' => CreatePackageAddon::route('/create'),
            'edit' => EditPackageAddon::route('/{record}/edit'),
        ];
    }
}
