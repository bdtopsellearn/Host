<?php

namespace App\Filament\Customer\Resources\Services\Pages;

use App\Filament\Customer\Resources\Services\ServiceResource;
use App\Models\Invoice;
use App\Models\Package;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected string $view = 'filament.customer.pages.manage-vps';

    public ?string $liveStatus = null;

    public function getTitle(): string
    {
        return 'Manage Server: ' . ($this->record->formatted_hostname ?? 'Virtual Private Server');
    }

    public function getHeading(): string
    {
        return 'Manage Server: ' . ($this->record->formatted_hostname ?? 'Virtual Private Server');
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/customer/services') => 'My Services',
            '' => $this->record->formatted_hostname ?? ('Server #' . $this->record->id),
        ];
    }

    public function mount(int | string $record): void
    {
        parent::mount($record);

        // Retrieve initial live power status
        $this->fetchLiveStatus();
    }

    public function fetchLiveStatus(): void
    {
        if (empty($this->record->contabo_instance_id)) {
            $this->liveStatus = $this->record->status;
            return;
        }

        try {
            $service = app(ProvisioningServiceInterface::class);
            $result = $service->getInstanceStatus($this->record->contabo_instance_id);

            if ($result->success) {
                $this->liveStatus = $result->data['status'] ?? 'running';
                
                // If IP was pending, update it
                if (!empty($result->data['ipAddress']) && ($this->record->ip_address === 'Pending IP' || empty($this->record->ip_address))) {
                    $this->record->update(['ip_address' => $result->data['ipAddress']]);
                }
            } else {
                $this->liveStatus = $this->record->status;
            }
        } catch (\Exception $e) {
            $this->liveStatus = $this->record->status;
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('upgrade')
                ->label('Upgrade Server')
                ->icon('heroicon-o-arrow-trending-up')
                ->color('primary')
                ->modalHeading('Upgrade Virtual Server (Scale Resources)')
                ->modalDescription('Scale your compute cores, RAM, and NVMe disk space. Downgrades are strictly disallowed to prevent disk filesystem corruption. Upgrades take effect immediately.')
                ->modalSubmitActionLabel('Confirm & Generate Upgrade Invoice')
                ->visible(fn () => in_array($this->record->status, ['active', 'contabo_ok', 'provisioned', 'awaiting_provisioning']))
                ->form(function () {
                    $currentPackage = $this->record->package;
                    $currentPrice = (float) ($currentPackage->price_monthly ?? 0);
                    
                    // Enforce No-Downgrades Rule: Only show strictly higher tiers
                    $higherPackages = Package::where('price_monthly', '>', $currentPrice)
                        ->orderBy('price_monthly', 'asc')
                        ->get()
                        ->mapWithKeys(function ($pkg) use ($currentPrice) {
                            $diff = (float) $pkg->price_monthly - $currentPrice;
                            return [$pkg->id => "{$pkg->name} (\${$pkg->price_monthly}/mo - +$" . number_format($diff, 2) . "/mo)"];
                        })
                        ->toArray();

                    if (empty($higherPackages)) {
                        return [
                            Placeholder::make('max_tier')
                                ->label('Maximum Tier Reached')
                                ->content('Your VPS is currently on our highest performance tier. Contact enterprise support for customized dedicated cluster nodes.'),
                        ];
                    }

                    return [
                        Select::make('target_package_id')
                            ->label('Select Target Plan')
                            ->options($higherPackages)
                            ->required(),
                    ];
                })
                ->action(function (array $data) {
                    $targetPackage = Package::find($data['target_package_id'] ?? null);
                    if (!$targetPackage) return;

                    $currentPackage = $this->record->package;
                    $currentPrice = (float) ($currentPackage->price_monthly ?? 0);
                    $newPrice = (float) $targetPackage->price_monthly;

                    if ($newPrice <= $currentPrice) {
                        Notification::make()->title('Downgrades are strictly not allowed')->danger()->send();
                        return;
                    }

                    // Calculate Prorated Math
                    $nextDue = $this->record->next_due_date ? Carbon::parse($this->record->next_due_date) : now()->addMonth();
                    $daysRemaining = max(1, now()->diffInDays($nextDue, false));
                    $daysInCycle = 30; // standard month cycle
                    $monthlyDiff = $newPrice - $currentPrice;
                    $proratedDue = max(0.50, round(($monthlyDiff / $daysInCycle) * $daysRemaining, 2));

                    // Generate Upgrade Invoice
                    $invoice = Invoice::create([
                        'user_id' => auth()->id(),
                        'order_id' => $this->record->order_id,
                        'invoice_number' => Invoice::generateNextNumber(),
                        'amount' => $proratedDue,
                        'tax' => 0,
                        'total' => $proratedDue,
                        'status' => 'pending',
                        'due_date' => now()->addDays(3),
                    ]);

                    // Adjust service recurring renewal rate
                    $oldRecurring = (float) $this->record->recurring_amount;
                    $newRecurring = $oldRecurring > 0 ? round($oldRecurring + $monthlyDiff, 2) : $newPrice;

                    $specsJson = is_string($targetPackage->specs) ? json_decode($targetPackage->specs, true) : (is_array($targetPackage->specs) ? $targetPackage->specs : []);

                    $this->record->update([
                        'package_id' => $targetPackage->id,
                        'recurring_amount' => $newRecurring,
                        'specs_snapshot' => [
                            'package_name' => $targetPackage->name,
                            'cores' => $specsJson['cores'] ?? 'N/A',
                            'memory' => $specsJson['memory'] ?? 'N/A',
                            'storage' => $specsJson['storage'] ?? 'N/A',
                            'bandwidth' => $specsJson['bandwidth'] ?? ($specsJson['port'] ?? '1 Gbps'),
                            'os' => $this->record->specs_snapshot['os'] ?? 'Ubuntu 24.04 LTS',
                            'datacenter' => $this->record->specs_snapshot['datacenter'] ?? 'US East (New York)',
                        ],
                    ]);

                    Notification::make()
                        ->title('Server Upgrade Registered!')
                        ->body("Upgraded from {$currentPackage->name} to {$targetPackage->name}. Prorated invoice #{$invoice->invoice_number} (\${$proratedDue}) generated.")
                        ->success()
                        ->send();
                }),

            Action::make('refresh_status')
                ->label('Refresh Status')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function () {
                    $this->fetchLiveStatus();
                    Notification::make()
                        ->title('Server Status Refreshed')
                        ->body("Current Status: " . strtoupper($this->liveStatus ?? 'UNKNOWN'))
                        ->info()
                        ->send();
                }),

            Action::make('start')
                ->label('Power On')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Power On Virtual Server')
                ->modalDescription('Are you sure you want to boot up and power on this server?')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->action(function (ProvisioningServiceInterface $service) {
                    $res = $service->startInstance($this->record->contabo_instance_id);
                    if ($res->success) {
                        $this->liveStatus = 'running';
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_STARTED',
                            'description' => "Powered on server {$this->record->formatted_hostname} ({$this->record->ip_address})",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Power On Initiated')->body('Your server is starting up.')->success()->send();
                    } else {
                        Notification::make()->title('Failed to Start Server')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('restart')
                ->label('Restart / Reboot')
                ->icon('heroicon-o-arrow-path-rounded-square')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Reboot Server')
                ->modalDescription('This will reboot your server. All unsaved system state in memory will be lost. Continue?')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->action(function (ProvisioningServiceInterface $service) {
                    $res = $service->rebootInstance($this->record->contabo_instance_id);
                    if ($res->success) {
                        $this->liveStatus = 'rebooting';
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_REBOOTED',
                            'description' => "Rebooted server {$this->record->formatted_hostname} ({$this->record->ip_address})",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Reboot Initiated')->body('Your server is now rebooting.')->success()->send();
                    } else {
                        Notification::make()->title('Failed to Reboot')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('stop')
                ->label('Power Off')
                ->icon('heroicon-o-stop')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Force Power Off')
                ->modalDescription('Stopping a compute instance is like pulling the power cord. Data may be lost if not cleanly saved. Continue?')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->action(function (ProvisioningServiceInterface $service) {
                    $res = $service->stopInstance($this->record->contabo_instance_id);
                    if ($res->success) {
                        $this->liveStatus = 'stopped';
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_STOPPED',
                            'description' => "Forced power-off on server {$this->record->formatted_hostname} ({$this->record->ip_address})",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Server Powered Off')->body('Your server has been powered off.')->warning()->send();
                    } else {
                        Notification::make()->title('Failed to Power Off')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('shutdown')
                ->label('Graceful Shutdown')
                ->icon('heroicon-o-power')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Clean ACPI Shutdown')
                ->modalDescription('This sends a clean shutdown ACPI signal to the guest operating system.')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->action(function (ProvisioningServiceInterface $service) {
                    $res = $service->shutdownInstance($this->record->contabo_instance_id);
                    if ($res->success) {
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_SHUTDOWN',
                            'description' => "Sent ACPI graceful shutdown signal to server {$this->record->formatted_hostname} ({$this->record->ip_address})",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Shutdown Signal Sent')->body('The OS has been instructed to shut down cleanly.')->info()->send();
                    } else {
                        Notification::make()->title('Failed to Shutdown')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('reset_password')
                ->label('Reset Password')
                ->icon('heroicon-o-key')
                ->color('info')
                ->modalHeading('Reset Root / Administrator Password')
                ->modalDescription('Set a new root password for your server. A cloud-init password update will be applied.')
                ->modalSubmitActionLabel('Update Password')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->form([
                    TextInput::make('new_password')
                        ->label('New Root Password')
                        ->password()
                        ->revealable()
                        ->default(fn () => Str::password(16, true, true, false, false) . 'A1!')
                        ->required()
                        ->helperText('Must be at least 10 characters long with numbers and letters.'),
                ])
                ->action(function (array $data, ProvisioningServiceInterface $service) {
                    $res = $service->resetPassword($this->record->contabo_instance_id, $data['new_password']);
                    if ($res->success) {
                        $this->record->update(['encrypted_credentials' => encrypt($data['new_password'])]);
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_PASSWORD_RESET',
                            'description' => "Reset root password for server {$this->record->formatted_hostname} ({$this->record->ip_address})",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Password Reset Successful')->body('Your root password has been updated.')->success()->send();
                    } else {
                        Notification::make()->title('Password Reset Failed')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('rescue')
                ->label('Rescue Mode')
                ->icon('heroicon-o-lifebuoy')
                ->color('danger')
                ->modalHeading('Boot into Linux Rescue System')
                ->modalDescription('Rescue system boots a lightweight Linux environment with your disk mounted for diagnostics and emergency data repair.')
                ->modalSubmitActionLabel('Boot into Rescue System')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->form([
                    TextInput::make('rescue_password')
                        ->label('Temporary Rescue Password')
                        ->default(fn () => Str::password(16, true, true, false, false) . 'A1!')
                        ->required(),
                ])
                ->action(function (array $data, ProvisioningServiceInterface $service) {
                    $res = $service->rescueInstance($this->record->contabo_instance_id, $data['rescue_password']);
                    if ($res->success) {
                        $this->liveStatus = 'rescue';
                        \App\Models\UserActivityLog::create([
                            'user_id' => auth()->id(),
                            'action' => 'SERVER_RESCUE_MODE',
                            'description' => "Booted server {$this->record->formatted_hostname} into rescue recovery mode",
                            'ip_address' => request()->ip(),
                        ]);
                        Notification::make()->title('Server in Rescue Mode')->body("Login with root / password: {$data['rescue_password']}")->warning()->duration(15000)->send();
                    } else {
                        Notification::make()->title('Rescue Mode Failed')->body($res->message)->danger()->send();
                    }
                }),

            Action::make('reinstall')
                ->label('Reinstall OS')
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('danger')
                ->modalHeading('Reinstall Server Operating System')
                ->modalDescription('WARNING: This will completely erase all data on the virtual server and install a fresh operating system.')
                ->modalSubmitActionLabel('Confirm & Wipe Server')
                ->visible(fn () => !empty($this->record->contabo_instance_id) && in_array($this->record->status, ['active', 'contabo_ok']))
                ->form([
                    Select::make('image_id')
                        ->label('Select Operating System')
                        ->options([
                            'afecbb85-e2fc-46f0-9684-b46b1faf00bb' => 'Ubuntu 22.04 LTS (Recommended)',
                            '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d' => 'Ubuntu 20.04 LTS',
                            'a2c26e8f-84a5-4f3e-9c0e-b06511928cc0' => 'Debian 12 Bookworm',
                            '7a8bffde-6721-44c0-ac03-c10796f455f8' => 'AlmaLinux 9',
                            'b1c23d4e-5f6a-7b8c-9d0e-1f2a3b4c5d6e' => 'Rocky Linux 9',
                            'c3d4e5f6-7a8b-9c0d-1e2f-3a4b5c6d7e8f' => 'Windows Server 2022 Datacenter',
                        ])
                        ->default('afecbb85-e2fc-46f0-9684-b46b1faf00bb')
                        ->required(),
                    TextInput::make('password')
                        ->label('New Root Password')
                        ->default(fn () => Str::password(16, true, true, false, false) . 'A1!')
                        ->required(),
                ])
                ->action(function (array $data, ProvisioningServiceInterface $service) {
                    $osMap = [
                        'afecbb85-e2fc-46f0-9684-b46b1faf00bb' => 'Ubuntu 22.04 LTS',
                        '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d' => 'Ubuntu 20.04 LTS',
                        'a2c26e8f-84a5-4f3e-9c0e-b06511928cc0' => 'Debian 12',
                        '7a8bffde-6721-44c0-ac03-c10796f455f8' => 'AlmaLinux 9',
                        'b1c23d4e-5f6a-7b8c-9d0e-1f2a3b4c5d6e' => 'Rocky Linux 9',
                        'c3d4e5f6-7a8b-9c0d-1e2f-3a4b5c6d7e8f' => 'Windows Server 2022',
                    ];
                    $osName = $osMap[$data['image_id']] ?? 'Linux OS';

                    $res = $service->reinstallInstance($this->record->contabo_instance_id, [
                        'image_id' => $data['image_id'],
                        'password' => $data['password'],
                        'default_user' => 'root',
                    ]);

                    if ($res->success) {
                        $this->record->update([
                            'encrypted_credentials' => encrypt($data['password']),
                            'os_image' => $osName,
                        ]);
                        $this->liveStatus = 'installing';
                        Notification::make()->title('Reinstallation Started')->body("Your server is being reinstalled with {$osName}.")->warning()->duration(10000)->send();
                    } else {
                        Notification::make()->title('Reinstallation Failed')->body($res->message)->danger()->send();
                    }
                }),
        ];
    }
}
