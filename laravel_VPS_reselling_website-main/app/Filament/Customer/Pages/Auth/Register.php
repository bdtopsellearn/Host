<?php

namespace App\Filament\Customer\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class Register extends BaseRegister
{
    public function getHeading(): string
    {
        return 'Register';
    }

    public function getSubheading(): \Illuminate\Contracts\Support\Htmlable | string | null
    {
        if (! filament()->hasLogin()) {
            return 'Deploy high-performance NVMe cloud VPS instances in seconds.';
        }

        return new \Illuminate\Support\HtmlString(
            '<span class="text-slate-500">Already have an account?</span> ' . $this->loginAction->toHtml()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getNameFormComponent(),
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
                Checkbox::make('terms')
                    ->label(new HtmlString('I have read and agree to the <a href="/terms-of-service" target="_blank" onclick="event.stopPropagation();" class="vortex-legal-link" style="color: inherit; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1.5px;">Terms of Service</a> and <a href="/privacy-policy" target="_blank" onclick="event.stopPropagation();" class="vortex-legal-link" style="color: inherit; font-weight: 600; text-decoration: underline; text-underline-offset: 3px; text-decoration-thickness: 1.5px;">Privacy Policy</a>.'))
                    ->accepted()
                    ->required()
                    ->dehydrated(false)
                    ->validationMessages([
                        'accepted' => 'You must agree to the Terms of Service and Privacy Policy to register.',
                        'required' => 'You must agree to the Terms of Service and Privacy Policy to register.',
                    ]),
            ]);
    }
}
