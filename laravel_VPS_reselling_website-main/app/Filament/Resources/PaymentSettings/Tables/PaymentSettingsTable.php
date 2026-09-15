<?php

namespace App\Filament\Resources\PaymentSettings\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('gateway_name')
                    ->label('Payment Method')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('account_number')
                    ->label('Number / Wallet ID')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Number copied to clipboard')
                    ->badge()
                    ->color('primary'),

                TextColumn::make('account_type')
                    ->label('Type')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('support_phone')
                    ->label('Support Phone')
                    ->searchable()
                    ->placeholder('None set'),

                TextColumn::make('conversion_rate_bdt_usd')
                    ->label('1 USD = BDT')
                    ->formatStateUsing(fn ($state) => $state ? "৳" . number_format((float) $state, 2) : '-')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->actions([
                Action::make('edit')
                    ->label('Edit Setup')
                    ->url(fn ($record): string => route('filament.admin.resources.payment-settings.edit', $record))
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
