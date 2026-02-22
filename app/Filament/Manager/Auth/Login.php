<?php

namespace App\Filament\Manager\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getRedirectUrl(): string
    {
        return '/manager';
    }
}