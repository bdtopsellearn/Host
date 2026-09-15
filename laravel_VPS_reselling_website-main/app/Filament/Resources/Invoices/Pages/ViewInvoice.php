<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify_explorer')
                ->label('Verify Explorer')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('info')
                ->visible(fn () => !empty($this->record->crypto_txid))
                ->url(fn () => (str_starts_with($this->record->crypto_txid ?? '', '0x') || str_contains($this->record->crypto_network ?? '', 'polygon'))
                    ? "https://polygonscan.com/tx/{$this->record->crypto_txid}"
                    : "https://tronscan.org/#/transaction/{$this->record->crypto_txid}", true)
                ->openUrlInNewTab(),

            Action::make('confirm_payment')
                ->label('Confirm Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm Payment Received')
                ->modalDescription('Confirm this invoice as PAID? This will mark the invoice as PAID, update the order status to "Payment Confirmed", and activate the deployment action on the order details page.')
                ->modalSubmitActionLabel('Confirm Payment')
                ->visible(fn () => in_array($this->record->status, ['pending', 'unpaid']))
                ->action(function () {
                    $this->record->update([
                        'status' => 'paid',
                        'paid_at' => now(),
                    ]);

                    if ($this->record->order) {
                        $this->record->order->update(['status' => 'payment_confirmed']);
                    }

                    if ($this->record->service_id) {
                        $this->record->service?->extendBillingCycle();
                        Notification::make()
                            ->title('Renewal Payment Confirmed for Invoice #' . $this->record->invoice_number)
                            ->body('Invoice marked as PAID. Service next due date has been automatically extended.')
                            ->success()
                            ->send();
                    } else {
                        \App\Models\Service::where('order_id', $this->record->order_id)->update(['status' => 'ready_for_provisioning']);

                        Notification::make()
                            ->title('Payment Confirmed for Invoice #' . $this->record->invoice_number)
                            ->body('Invoice marked as PAID. Order is now ready for deployment.')
                            ->success()
                            ->send();
                    }
                }),

            Action::make('open_order')
                ->label('Open Order to Deploy →')
                ->icon('heroicon-o-arrow-right')
                ->color('primary')
                ->visible(fn () => $this->record->status === 'paid' && !empty($this->record->order_id))
                ->url(fn () => OrderResource::getUrl('view', ['record' => $this->record->order_id])),

            ActionGroup::make([
                Action::make('cancel_invoice')
                    ->label('Cancel Invoice')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Cancel Invoice')
                    ->modalDescription('Are you sure you want to cancel this invoice? This action cannot be undone.')
                    ->visible(fn () => $this->record->status !== 'cancelled')
                    ->action(function () {
                        $this->record->update(['status' => 'cancelled']);
                        Notification::make()->title('Invoice Cancelled')->success()->send();
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

