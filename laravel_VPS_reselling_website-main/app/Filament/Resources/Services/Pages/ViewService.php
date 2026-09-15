<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Credentials Action
            Action::make('credentials')
                ->label('View VPS Credentials')
                ->icon('heroicon-o-key')
                ->color('info')
                ->modalHeading('VPS Login Credentials')
                ->visible(fn () => !empty($this->record->ip_address))
                ->form(function () {
                    $record = $this->record;
                    $pass = $record->decrypted_password ?? 'N/A';
                    $user = $record->default_user ?? 'root';
                    $ip = $record->ip_address ?? 'N/A';

                    return [
                        TextInput::make('ip')
                            ->label('Primary IP Address')
                            ->default($ip)
                            ->disabled(),
                        TextInput::make('user')
                            ->label('Login Username')
                            ->default($user)
                            ->disabled(),
                        TextInput::make('password')
                            ->label('Root / Admin Password')
                            ->default($pass)
                            ->disabled(),
                        Placeholder::make('ssh')
                            ->label('SSH Connection Command')
                            ->content("ssh {$user}@{$ip}"),
                    ];
                }),

            // Server Power Actions
            ActionGroup::make([
                Action::make('start')
                    ->label('Power On')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn () => !empty($this->record->contabo_instance_id))
                    ->action(function (ProvisioningServiceInterface $service) {
                        $res = $service->startInstance($this->record->contabo_instance_id);
                        if ($res->success) {
                            Notification::make()->title('Server Powered On')->success()->send();
                        } else {
                            Notification::make()->title('Failed')->body($res->message)->danger()->send();
                        }
                    }),

                Action::make('stop')
                    ->label('Power Off (Force Stop)')
                    ->icon('heroicon-o-stop')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn () => !empty($this->record->contabo_instance_id))
                    ->action(function (ProvisioningServiceInterface $service) {
                        $res = $service->stopInstance($this->record->contabo_instance_id);
                        if ($res->success) {
                            Notification::make()->title('Server Powered Off')->success()->send();
                        } else {
                            Notification::make()->title('Failed')->body($res->message)->danger()->send();
                        }
                    }),

                Action::make('restart')
                    ->label('Reboot / Restart')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn () => !empty($this->record->contabo_instance_id))
                    ->action(function (ProvisioningServiceInterface $service) {
                        $res = $service->rebootInstance($this->record->contabo_instance_id);
                        if ($res->success) {
                            Notification::make()->title('Server Reboot Initiated')->success()->send();
                        } else {
                            Notification::make()->title('Failed')->body($res->message)->danger()->send();
                        }
                    }),

                Action::make('shutdown')
                    ->label('Graceful Shutdown')
                    ->icon('heroicon-o-power')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->visible(fn () => !empty($this->record->contabo_instance_id))
                    ->action(function (ProvisioningServiceInterface $service) {
                        $res = $service->shutdownInstance($this->record->contabo_instance_id);
                        if ($res->success) {
                            Notification::make()->title('Shutdown Signal Sent')->success()->send();
                        } else {
                            Notification::make()->title('Failed')->body($res->message)->danger()->send();
                        }
                    }),

                Action::make('reset_password')
                    ->label('Reset Root Password')
                    ->icon('heroicon-o-lock-closed')
                    ->color('warning')
                    ->form([
                        TextInput::make('new_password')
                            ->label('New Root Password')
                            ->default(fn () => Str::password(16, true, true, false, false) . 'A1!')
                            ->required(),
                    ])
                    ->action(function (array $data, ProvisioningServiceInterface $service) {
                        $res = $service->resetPassword($this->record->contabo_instance_id, $data['new_password']);
                        if ($res->success) {
                            $this->record->update(['encrypted_credentials' => encrypt($data['new_password'])]);
                            Notification::make()->title('Password Reset Successful')->body("New password: {$data['new_password']}")->success()->send();
                        } else {
                            Notification::make()->title('Password Reset Failed')->body($res->message)->danger()->send();
                        }
                    }),

                Action::make('rescue')
                    ->label('Boot into Rescue Mode')
                    ->icon('heroicon-o-lifebuoy')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('rescue_password')
                            ->label('Rescue Mode Password')
                            ->default(fn () => Str::password(16, true, true, false, false) . 'A1!')
                            ->required(),
                    ])
                    ->action(function (array $data, ProvisioningServiceInterface $service) {
                        $res = $service->rescueInstance($this->record->contabo_instance_id, $data['rescue_password']);
                        if ($res->success) {
                            Notification::make()->title('Server in Rescue Mode')->body("Rescue Password: {$data['rescue_password']}")->warning()->send();
                        } else {
                            Notification::make()->title('Rescue Mode Failed')->body($res->message)->danger()->send();
                        }
                    }),
            ])
                ->label('Power Actions')
                ->icon('heroicon-m-bolt')
                ->color('primary'),

            // Adjust Renewal Rate
            Action::make('adjust_rate')
                ->label('Adjust Rate')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning')
                ->modalHeading('Adjust Service Recurring Renewal Rate')
                ->modalDescription('Manually increase or decrease this client\'s recurring renewal fee for upcoming billing cycles.')
                ->form([
                    TextInput::make('recurring_amount')
                        ->label('New Recurring Renewal Amount ($)')
                        ->prefix('$')
                        ->numeric()
                        ->default(fn () => $this->record->recurring_amount)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $oldRate = $this->record->recurring_amount;
                    $this->record->update(['recurring_amount' => $data['recurring_amount']]);
                    Notification::make()
                        ->title('Renewal Rate Adjusted')
                        ->body("Service #{$this->record->id} rate changed from \${$oldRate} to \${$data['recurring_amount']}.")
                        ->success()
                        ->send();
                }),

            // More Actions
            ActionGroup::make([
                EditAction::make(),
                DeleteAction::make(),
            ])
                ->label('More Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray'),
        ];
    }
}
