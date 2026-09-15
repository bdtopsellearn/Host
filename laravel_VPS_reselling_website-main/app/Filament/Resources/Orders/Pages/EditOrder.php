<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Service;
use App\Services\Provisioning\ProvisioningServiceInterface;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use App\Mail\ServiceDeliveredMail;
use Illuminate\Support\Facades\Mail;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('cancel_order')
                ->label('Cancel')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Cancel Order')
                ->visible(fn () => !in_array($this->record->status, ['cancelled', 'active']))
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    Notification::make()->title('Order Cancelled')->success()->send();
                }),

            Action::make('provision_contabo')
                ->label('Go to Contabo')
                ->icon('heroicon-o-cloud-arrow-up')
                ->color('warning')
                ->modalHeading('Provision VPS via Contabo API')
                ->visible(fn () => in_array($this->record->status, ['pending', 'provision', 'failed']))
                ->form(function () {
                    $service = $this->record->services()->first();
                    $package = $service?->package;
                    $defaultProductId = $package?->contabo_product_id ?? 'V153';

                    return [
                        TextInput::make('product_id')
                            ->label('Contabo Product ID')
                            ->default($defaultProductId)
                            ->required(),
                        Select::make('region')
                            ->label('Datacenter Region')
                            ->options([
                                'EU' => 'European Union (Germany)',
                                'US-central' => 'United States (Central)',
                            ])
                            ->default('EU')
                            ->required(),
                        Select::make('image_id')
                            ->label('OS Image')
                            ->options([
                                'afecbb85-e2fc-46f0-9684-b46b1faf00bb' => 'Ubuntu 22.04 LTS',
                            ])
                            ->default('afecbb85-e2fc-46f0-9684-b46b1faf00bb')
                            ->required(),
                        TextInput::make('default_user')->default('root')->required(),
                        TextInput::make('root_password')->default(fn () => Str::password(16, true, true, false, false) . 'A1!')->required(),
                        TextInput::make('display_name')->default('VPS-' . $this->record->order_number),
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
                        $orderPayload = array_merge($data, ['service_id' => $service->id, 'period' => 1]);
                        $result = $provisioningService->createInstance($orderPayload);

                        if ($result->success) {
                            $service->update([
                                'contabo_instance_id' => $result->data['instanceId'] ?? '',
                                'ip_address' => $result->data['ipAddress'] ?? 'Pending IP',
                                'encrypted_credentials' => encrypt($result->data['initialPassword'] ?? $data['root_password']),
                                'default_user' => $result->data['defaultUser'] ?? $data['default_user'],
                                'server_name' => $data['display_name'],
                                'status' => 'contabo_ok',
                            ]);
                            $successCount++;
                        } else {
                            $lastError = $result->message;
                        }
                    }

                    if ($successCount > 0) {
                        $this->record->update(['status' => 'contabo_ok']);
                        Notification::make()->title('Provisioned on Contabo!')->success()->send();
                    } else {
                        $this->record->update(['status' => 'failed']);
                        Notification::make()->title('Provisioning Failed')->body($lastError)->danger()->send();
                    }
                }),

            Action::make('accept_and_deliver')
                ->label('Accept & Deliver')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'contabo_ok')
                ->action(function () {
                    $this->record->update(['status' => 'active']);
                    foreach ($this->record->services as $service) {
                        $service->update(['status' => 'active', 'next_due_date' => now()->addMonth()]);
                        try {
                            Mail::to($this->record->user->email)->send(new ServiceDeliveredMail(
                                $service, $this->record->user, $service->decrypted_password ?? 'N/A', $service->default_user ?? 'root'
                            ));
                        } catch (\Exception $e) {}
                    }
                    Notification::make()->title('Service Delivered!')->success()->send();
                }),

            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
