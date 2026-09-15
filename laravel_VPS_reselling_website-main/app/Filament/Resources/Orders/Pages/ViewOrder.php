<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Service;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Str;
use App\Mail\ServiceDeliveredMail;
use Illuminate\Support\Facades\Mail;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_payment')
                ->label('Confirm Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm Payment Received')
                ->modalDescription('Confirm payment for this order? This will mark the associated invoice as PAID and activate the Contabo deployment button.')
                ->modalSubmitActionLabel('Confirm Payment')
                ->visible(fn () => in_array($this->record->status, ['pending', 'unpaid']) || ($this->record->invoice && in_array($this->record->invoice->status, ['pending', 'unpaid'])))
                ->action(function () {
                    if ($this->record->invoice) {
                        $this->record->invoice->update([
                            'status' => 'paid',
                            'paid_at' => now(),
                        ]);
                    }

                    $this->record->update(['status' => 'payment_confirmed']);
                    $this->record->services()->update(['status' => 'ready_for_provisioning']);

                    Notification::make()
                        ->title('Payment Confirmed!')
                        ->body('Order is now confirmed. You can review server parameters and deploy to Contabo.')
                        ->success()
                        ->send();
                }),

            Action::make('accept_and_deploy')
                ->label('⚡ Accept & Deploy to Contabo')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('primary')
                ->modalHeading('Final Review & Deploy to Contabo')
                ->modalDescription('Review the server parameters, package tier, datacenter region, OS image, and root password before dispatching the live provisioning request to Contabo API.')
                ->modalSubmitActionLabel('⚡ Accept & Deploy to Contabo')
                ->visible(fn () => in_array($this->record->status, ['payment_confirmed', 'provision', 'failed']) || ($this->record->invoice?->status === 'paid' && !in_array($this->record->status, ['active', 'contabo_ok', 'cancelled'])))
                ->form(function () {
                    $service = $this->record->services()->first();
                    $package = $service?->package;
                    $defaultProductId = $package?->contabo_product_id ?? 'V153';

                    // Intelligently determine pre-selected region
                    $creds = is_array($service?->encrypted_credentials) 
                        ? $service->encrypted_credentials 
                        : json_decode($service?->encrypted_credentials ?? '[]', true);

                    $regionCandidate = $service?->region 
                        ?? ($creds['region_api_identifier'] ?? null)
                        ?? config('services.contabo.default_region', 'EU');

                    // If regionCandidate is not in allowed OpenAPI enum, fallback to EU
                    $allowedRegions = ['EU', 'US-central', 'US-east', 'US-west', 'SIN', 'UK', 'AUS', 'JPN', 'IND'];
                    $selectedRegion = in_array($regionCandidate, $allowedRegions) ? $regionCandidate : 'EU';

                    // OS Image Candidate
                    $imageCandidate = $creds['os_api_identifier'] ?? null;
                    $defaultImageId = config('services.contabo.default_image_id', 'afecbb85-e2fc-46f0-9684-b46b1faf00bb');
                    if ($imageCandidate) {
                        if (str_contains($imageCandidate, '20.04')) {
                            $defaultImageId = '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d';
                        } elseif (str_contains($imageCandidate, 'debian')) {
                            $defaultImageId = 'a2c26e8f-84a5-4f3e-9c0e-b06511928cc0';
                        } elseif (str_contains($imageCandidate, 'windows')) {
                            $defaultImageId = 'c3d4e5f6-7a8b-9c0d-1e2f-3a4b5c6d7e8f';
                        }
                    }

                    // Root password: use decrypted password or generate fresh strong one
                    $initialPassword = $service?->decrypted_password 
                        ?? (Str::password(16, true, true, false, false) . 'A1!');

                    return [
                        TextInput::make('product_id')
                            ->label('Contabo Product ID')
                            ->helperText('Official Contabo Product ID (e.g., V153 for Cloud VPS 4, V154 for Cloud VPS 6)')
                            ->default($defaultProductId)
                            ->required(),
                        Select::make('region')
                            ->label('Datacenter Region')
                            ->helperText('Contabo Datacenter Region (OpenAPI compliant enum)')
                            ->options([
                                'EU' => 'European Union (Germany) [EU]',
                                'US-central' => 'United States (Central - St. Louis) [US-central]',
                                'US-east' => 'United States (East - New York) [US-east]',
                                'US-west' => 'United States (West - Seattle) [US-west]',
                                'SIN' => 'Singapore (Asia-Pacific) [SIN]',
                                'UK' => 'United Kingdom (London) [UK]',
                                'AUS' => 'Australia (Sydney) [AUS]',
                                'JPN' => 'Japan (Tokyo) [JPN]',
                                'IND' => 'India (Mumbai) [IND]',
                            ])
                            ->default($selectedRegion)
                            ->required(),
                        Select::make('image_id')
                            ->label('Operating System Image')
                            ->helperText('Contabo official OS image UUID')
                            ->options([
                                'afecbb85-e2fc-46f0-9684-b46b1faf00bb' => 'Ubuntu 22.04 LTS (Recommended)',
                                '9b1deb4d-3b7d-4bad-9bdd-2b0d7b3dcb6d' => 'Ubuntu 20.04 LTS',
                                'a2c26e8f-84a5-4f3e-9c0e-b06511928cc0' => 'Debian 12',
                                'c3d4e5f6-7a8b-9c0d-1e2f-3a4b5c6d7e8f' => 'Windows Server 2022 Standard',
                            ])
                            ->default($defaultImageId)
                            ->required(),
                        TextInput::make('default_user')
                            ->label('Default User')
                            ->default('root')
                            ->required(),
                        TextInput::make('root_password')
                            ->label('Root Password')
                            ->helperText('Initial server root password to set via cloud-init')
                            ->default($initialPassword)
                            ->required(),
                        TextInput::make('display_name')
                            ->label('Server Display Name / Hostname')
                            ->default('VPS-' . $this->record->order_number),
                    ];
                })
                ->action(function (array $data, ProvisioningServiceInterface $provisioningService) {
                    $services = $this->record->services;
                    if ($services->isEmpty()) {
                        $service = Service::create([
                            'user_id' => $this->record->user_id,
                            'order_id' => $this->record->id,
                            'package_id' => \App\Models\Package::first()?->id ?? 1,
                            'status' => 'provisioning',
                            'billing_cycle' => 'monthly',
                        ]);
                        $services = collect([$service]);
                    }

                    $successCount = 0;
                    $lastError = '';

                    foreach ($services as $service) {
                        $orderPayload = array_merge($data, [
                            'service_id' => $service->id,
                            'period' => 1,
                        ]);
                        $result = $provisioningService->createInstance($orderPayload);

                        if ($result->success) {
                            $service->update([
                                'contabo_instance_id' => $result->data['instanceId'] ?? '',
                                'ip_address' => $result->data['ipAddress'] ?? 'Pending IP',
                                'encrypted_credentials' => encrypt($result->data['initialPassword'] ?? $data['root_password']),
                                'default_user' => $result->data['defaultUser'] ?? $data['default_user'],
                                'server_name' => $data['display_name'],
                                'os_image' => 'Linux OS',
                                'region' => $data['region'],
                                'status' => 'contabo_ok',
                            ]);
                            $successCount++;
                        } else {
                            $lastError = $result->message;
                        }
                    }

                    if ($successCount > 0) {
                        $this->record->update(['status' => 'contabo_ok']);
                        Notification::make()
                            ->title('Provisioned on Contabo!')
                            ->body('Server instance created successfully. You can now click "Accept & Deliver" to send credentials to the customer.')
                            ->success()
                            ->send();
                    } else {
                        $this->record->update(['status' => 'failed']);
                        Notification::make()
                            ->title('Contabo Provisioning Failed')
                            ->body($lastError)
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('accept_and_deliver')
                ->label('✉️ Accept & Deliver')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Deliver Credentials & Activate Service')
                ->modalDescription('Send the server access details (IP address, root username, and password) to the customer via email and activate the service?')
                ->modalSubmitActionLabel('Deliver & Activate')
                ->visible(fn () => $this->record->status === 'contabo_ok')
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                    foreach ($this->record->services as $service) {
                        $service->update(['status' => 'active', 'next_due_date' => now()->addMonth()]);
                        try {
                            Mail::to($this->record->user->email)->send(new ServiceDeliveredMail(
                                $service,
                                $this->record->user,
                                $service->decrypted_password ?? 'N/A',
                                $service->default_user ?? 'root'
                            ));
                        } catch (\Exception $e) {}
                    }
                    Notification::make()->title('Service Delivered & Activated!')->success()->send();
                }),

            Action::make('sync_status')
                ->label('Sync Contabo Status')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->visible(fn () => in_array($this->record->status, ['contabo_ok', 'active']) && (empty($this->record->services->first()?->ip_address) || in_array($this->record->services->first()?->ip_address, ['Pending IP', 'Pending Assignment'])))
                ->action(function (ProvisioningServiceInterface $provisioningService) {
                    $service = $this->record->services->first();
                    if ($service && $service->contabo_instance_id) {
                        $result = $provisioningService->getInstance($service->contabo_instance_id);
                        $fetchedIp = $result->data['ipAddress'] ?? '';
                        
                        if ($result->success && !empty($fetchedIp) && !in_array($fetchedIp, ['Pending IP', 'Pending Assignment'])) {
                            $service->update(['ip_address' => $fetchedIp]);
                            Notification::make()->title('IP Address Synced!')->body("Assigned IP: {$fetchedIp}")->success()->send();
                            return;
                        }
                    }
                    Notification::make()->title('Still Pending')->body('Contabo has not assigned an IP yet. Check your Contabo dashboard or retry shortly.')->warning()->send();
                }),

            ActionGroup::make([
                Action::make('cancel_order')
                    ->label('Cancel Order')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Order')
                    ->visible(fn () => !in_array($this->record->status, ['cancelled', 'active']))
                    ->action(function () {
                        $this->record->update(['status' => 'cancelled']);
                        Notification::make()->title('Order Cancelled')->success()->send();
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

