<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = null;

    protected static string|\UnitEnum|null $navigationGroup = 'Users';

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('All Users')
                ->group('Users')
                ->sort(1)
                ->badge(fn () => User::count() ?: null)
                ->url(static::getUrl('index', ['tab' => 'all']))
                ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.users.index') && (!request()->has('tab') || request()->get('tab') === 'all')),

            NavigationItem::make('Active Users')
                ->group('Users')
                ->sort(2)
                ->badge(fn () => User::activeCustomer()->count() ?: null, color: 'success')
                ->url(static::getUrl('index', ['tab' => 'active']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'active'),

            NavigationItem::make('Suspended Users')
                ->group('Users')
                ->sort(3)
                ->badge(fn () => User::suspendedCustomer()->count() ?: null, color: 'warning')
                ->url(static::getUrl('index', ['tab' => 'suspended']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'suspended'),

            NavigationItem::make('Inactive Users')
                ->group('Users')
                ->sort(4)
                ->badge(fn () => User::inactiveCustomer()->count() ?: null, color: 'gray')
                ->url(static::getUrl('index', ['tab' => 'inactive']))
                ->isActiveWhen(fn (): bool => request()->get('tab') === 'inactive'),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return \App\Filament\Resources\Users\Infolists\UserInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\OrdersRelationManager::class,
            RelationManagers\ServicesRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\CreditsRelationManager::class,
            RelationManagers\TransactionsRelationManager::class,
            RelationManagers\SupportTicketsRelationManager::class,
            RelationManagers\EmailsRelationManager::class,
            RelationManagers\NotesRelationManager::class,
            RelationManagers\ActivityLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => \App\Filament\Resources\Users\Pages\ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
