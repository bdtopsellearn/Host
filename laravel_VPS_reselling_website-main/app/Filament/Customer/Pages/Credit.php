<?php

namespace App\Filament\Customer\Pages;

use App\Models\Invoice;
use App\Models\UserActivityLog;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;

class Credit extends Page
{
    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Credit';

    protected static string | \BackedEnum | null $navigationIcon = null;

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.customer.pages.credit';

    public float $depositAmount = 50.00;

    public string $paymentMethod = 'crypto';

    public function getTitle(): string | Htmlable
    {
        return 'Account Credit & Balance';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Account Credit & Balance';
    }

    public function getSubheading(): ?string
    {
        return 'Pre-fund your VortexCloud balance for zero-downtime automated VPS renewals and instant service provisioning.';
    }

    public function selectPreset(float $amount): void
    {
        $this->depositAmount = $amount;
    }

    public function createDeposit(): mixed
    {
        $amount = round((float) $this->depositAmount, 2);

        if ($amount < 5.00) {
            Notification::make()
                ->title('Minimum Deposit is $5.00')
                ->warning()
                ->send();
            return null;
        }

        if ($amount > 2500.00) {
            Notification::make()
                ->title('Maximum Single Deposit is $2,500.00')
                ->warning()
                ->send();
            return null;
        }

        $user = auth()->user();

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'order_id' => null,
            'service_id' => null,
            'amount' => $amount,
            'tax' => 0.00,
            'total' => $amount,
            'status' => 'unpaid',
            'payment_method' => 'crypto',
            'due_date' => now()->toDateString(),
        ]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'CREDIT_DEPOSIT_INITIATED',
            'description' => "Created deposit invoice #{$invoice->invoice_number} for \${$amount} via CRYPTO",
            'ip_address' => request()->ip(),
        ]);

        Notification::make()
            ->title('Deposit Invoice Generated')
            ->body("Invoice #{$invoice->invoice_number} for \${$amount} created. Redirecting to crypto payment...")
            ->success()
            ->send();

        return redirect()->route('checkout.crypto-pay', $invoice->id);
    }

    public function getDepositInvoices(): Collection
    {
        return Invoice::where('user_id', auth()->id())
            ->whereNull('order_id')
            ->whereNull('service_id')
            ->latest()
            ->limit(20)
            ->get();
    }
}
