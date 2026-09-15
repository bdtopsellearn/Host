<?php

namespace App\Filament\Resources\PaymentSettings\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('gateway_key')
                    ->label('Gateway Key Identifier')
                    ->options([
                        'bkash' => 'bKash (বিকাশ)',
                        'nagad' => 'Nagad (নগদ)',
                        'rocket' => 'Rocket (রকেট)',
                        'binance' => 'Binance Pay / USDT Crypto',
                        'stripe' => 'Stripe Card (International)',
                    ])
                    ->required(),

                TextInput::make('gateway_name')
                    ->label('Display Title for Customers')
                    ->placeholder('e.g. bKash (বিকাশ - পার্সোনাল)')
                    ->required(),

                TextInput::make('account_number')
                    ->label('Account / Wallet / Mobile Number')
                    ->placeholder('e.g. 01619789895 or Binance Pay ID')
                    ->helperText('Customers will send payment to this number or address.')
                    ->required(),

                Select::make('account_type')
                    ->label('Account Type')
                    ->options([
                        'Personal' => 'Personal (Send Money)',
                        'Merchant' => 'Merchant (Make Payment)',
                        'Agent' => 'Agent (Cash Out)',
                        'Binance Pay & TRC20/Polygon' => 'Crypto / Binance Pay',
                    ])
                    ->default('Personal')
                    ->required(),

                TextInput::make('support_phone')
                    ->label('Customer Contact / Hotline Phone')
                    ->placeholder('e.g. 01619789895')
                    ->helperText('Displayed to customers during checkout if they need direct phone assistance.'),

                TextInput::make('support_whatsapp')
                    ->label('Support WhatsApp Number')
                    ->placeholder('e.g. 01619789895'),

                TextInput::make('conversion_rate_bdt_usd')
                    ->label('BDT to USD Exchange Rate')
                    ->numeric()
                    ->default(125.00)
                    ->helperText('1 USD = how many BDT (e.g. 125.00)'),

                Toggle::make('is_active')
                    ->label('Enable Gateway (Visible in Checkout)')
                    ->default(true),

                Textarea::make('instructions')
                    ->label('Payment Instructions for Customers')
                    ->placeholder('Explain step-by-step how to pay...')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
