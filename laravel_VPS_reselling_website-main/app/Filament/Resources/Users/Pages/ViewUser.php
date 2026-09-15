<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\ActionGroup;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function getContentTabLabel(): ?string
    {
        return 'Profile';
    }

    public function getContentTabIcon(): ?string
    {
        return 'heroicon-o-user-circle';
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                \Filament\Actions\Action::make('suspend')
                    ->label('Block Portal Login')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Block Customer Portal Login')
                    ->modalDescription('Are you sure you want to block this user? They will immediately lose access to their customer dashboard. Their server instances will remain unchanged.')
                    ->visible(fn () => !$this->record->is_suspended)
                    ->action(function () {
                        $this->record->update(['is_suspended' => true]);
                        \App\Models\UserActivityLog::create([
                            'user_id' => $this->record->id,
                            'action' => 'PORTAL_BLOCKED',
                            'description' => 'Staff blocked customer portal access',
                            'ip_address' => request()->ip(),
                        ]);
                        \Filament\Notifications\Notification::make()->title('Portal Login Blocked')->danger()->send();
                    }),
                \Filament\Actions\Action::make('unsuspend')
                    ->label('Restore Portal Login')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Customer Portal Login')
                    ->modalDescription('Are you sure you want to restore this user\'s access to their customer dashboard?')
                    ->visible(fn () => $this->record->is_suspended)
                    ->action(function () {
                        $this->record->update(['is_suspended' => false]);
                        \App\Models\UserActivityLog::create([
                            'user_id' => $this->record->id,
                            'action' => 'PORTAL_RESTORED',
                            'description' => 'Staff restored customer portal access',
                            'ip_address' => request()->ip(),
                        ]);
                        \Filament\Notifications\Notification::make()->title('Portal Login Restored')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->label('More Actions')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('gray'),
        ];
    }
}
