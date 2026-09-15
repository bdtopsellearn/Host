<?php

namespace App\Filament\Customer\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EditProfile extends BaseEditProfile
{
    public static function isSimple(): bool
    {
        return false;
    }

    public function getUser(): \Illuminate\Contracts\Auth\Authenticatable & \Illuminate\Database\Eloquent\Model
    {
        $user = \Filament\Facades\Filament::auth()->user() ?? auth()->user();

        if (! $user instanceof \Illuminate\Database\Eloquent\Model) {
            throw new \LogicException('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }

    public function getHeading(): string
    {
        return 'Account & Profile Settings';
    }

    public function getSubheading(): ?string
    {
        return 'Manage your personal details, billing address, security credentials, and email notification preferences.';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Personal & Organization Details
                Section::make('Personal & Organization Profile')
                    ->description('Your primary account identity, login email, and company information.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                $this->getNameFormComponent()
                                    ->label('Full Name')
                                    ->placeholder('John Doe'),

                                $this->getEmailFormComponent()
                                    ->label('Email Address')
                                    ->placeholder('john@company.com'),

                                TextInput::make('company_name')
                                    ->label('Company / Organization Name')
                                    ->placeholder('e.g. Acme Cloud Corp')
                                    ->maxLength(255),

                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->placeholder('+1 (555) 234-5678')
                                    ->maxLength(50),
                            ]),
                    ]),

                // 2. Billing & Contact Address (WHMCS Match)
                Section::make('Billing & Postal Address')
                    ->description('Primary billing details for invoice generation, VAT compliance, and region routing.')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('address')
                                    ->label('Street Address')
                                    ->placeholder('123 Innovation Way, Suite 400')
                                    ->columnSpan(2)
                                    ->maxLength(255),

                                TextInput::make('city')
                                    ->label('City')
                                    ->placeholder('Newark')
                                    ->maxLength(100),

                                TextInput::make('state')
                                    ->label('State / Province / Region')
                                    ->placeholder('Delaware')
                                    ->maxLength(100),

                                Select::make('country')
                                    ->label('Country')
                                    ->searchable()
                                    ->placeholder('Select your country')
                                    ->options([
                                        'United States' => 'United States',
                                        'Germany' => 'Germany',
                                        'United Kingdom' => 'United Kingdom',
                                        'Canada' => 'Canada',
                                        'Australia' => 'Australia',
                                        'France' => 'France',
                                        'Netherlands' => 'Netherlands',
                                        'Singapore' => 'Singapore',
                                        'Japan' => 'Japan',
                                        'India' => 'India',
                                        'Bangladesh' => 'Bangladesh',
                                        'Brazil' => 'Brazil',
                                        'United Arab Emirates' => 'United Arab Emirates',
                                        'Turkey' => 'Turkey',
                                        'Spain' => 'Spain',
                                        'Italy' => 'Italy',
                                        'Poland' => 'Poland',
                                        'Sweden' => 'Sweden',
                                        'Switzerland' => 'Switzerland',
                                        'Ireland' => 'Ireland',
                                        'Austria' => 'Austria',
                                        'Norway' => 'Norway',
                                        'Finland' => 'Finland',
                                        'Denmark' => 'Denmark',
                                        'Belgium' => 'Belgium',
                                        'New Zealand' => 'New Zealand',
                                    ]),

                                TextInput::make('zip_code')
                                    ->label('Postal / Zip Code')
                                    ->placeholder('19702')
                                    ->maxLength(30),
                            ]),
                    ]),

                // 3. Security & Password Management
                Section::make('Security & Authentication')
                    ->description('Leave password fields blank if you do not want to change your current password.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                $this->getCurrentPasswordFormComponent(),
                            ]),
                        Grid::make(2)
                            ->schema([
                                $this->getPasswordFormComponent()
                                    ->label('New Password'),
                                $this->getPasswordConfirmationFormComponent()
                                    ->label('Confirm New Password'),
                            ]),
                    ]),

                // 4. Notification & Email Preferences
                Section::make('Notification & Email Preferences')
                    ->description('Control which operational notices and billing alerts are sent to your inbox.')
                    ->icon('heroicon-o-bell')
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                Toggle::make('notification_preferences.invoices')
                                    ->label('Billing & Invoice Alerts')
                                    ->helperText('Receive email notifications when new invoices are generated, overdue reminders, and payment receipts.')
                                    ->default(true),

                                Toggle::make('notification_preferences.maintenance')
                                    ->label('Infrastructure & Telemetry Notices')
                                    ->helperText('Get advance warnings for scheduled hypervisor maintenance, host node reboots, and network incidents.')
                                    ->default(true),

                                Toggle::make('notification_preferences.security')
                                    ->label('Security & Authentication Alerts')
                                    ->helperText('Receive alerts for account password updates, API token generation, and suspicious login activity.')
                                    ->default(true),
                            ]),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['notification_preferences'] = array_merge([
            'invoices' => true,
            'maintenance' => true,
            'security' => true,
        ], $data['notification_preferences'] ?? []);

        return $data;
    }

    protected function afterSave(): void
    {
        $user = $this->getUser();
        $ip = request()->ip();

        // 1. Audit Password Updates
        if (!empty($this->data['password'])) {
            \App\Models\UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'PASSWORD_CHANGED',
                'description' => 'Customer updated account security password from profile settings',
                'ip_address' => $ip,
            ]);
        }

        // 2. Audit Profile & Contact Details Changes
        $changedFields = [];
        $tracked = ['name', 'email', 'phone', 'company_name', 'address', 'city', 'state', 'country', 'zip_code'];
        foreach ($tracked as $field) {
            if ($user->wasChanged($field)) {
                $changedFields[] = ucwords(str_replace('_', ' ', $field));
            }
        }

        if (!empty($changedFields)) {
            \App\Models\UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'PROFILE_UPDATED',
                'description' => 'Customer updated profile details: ' . implode(', ', $changedFields),
                'ip_address' => $ip,
            ]);
        }
    }
}
