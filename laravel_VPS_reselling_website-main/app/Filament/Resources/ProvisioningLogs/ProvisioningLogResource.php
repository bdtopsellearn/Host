<?php

namespace App\Filament\Resources\ProvisioningLogs;

use App\Filament\Resources\ProvisioningLogs\Pages\CreateProvisioningLog;
use App\Filament\Resources\ProvisioningLogs\Pages\EditProvisioningLog;
use App\Filament\Resources\ProvisioningLogs\Pages\ListProvisioningLogs;
use App\Filament\Resources\ProvisioningLogs\Schemas\ProvisioningLogForm;
use App\Filament\Resources\ProvisioningLogs\Tables\ProvisioningLogsTable;
use App\Models\ProvisioningLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ProvisioningLogResource extends Resource
{
    protected static ?string $model = ProvisioningLog::class;

    protected static ?string $navigationLabel = 'API Error Logs';
    protected static string|\UnitEnum|null $navigationGroup = 'System Settings';
    protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'API Log';
    protected static ?string $pluralModelLabel = 'API Logs';

    protected static string|BackedEnum|null $navigationIcon = null;

    public static function form(Schema $schema): Schema
    {
        return ProvisioningLogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProvisioningLogsTable::configure($table);
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
            'index' => ListProvisioningLogs::route('/'),
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
    
    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }
}
