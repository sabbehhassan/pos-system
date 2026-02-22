<?php

namespace App\Filament\Admin\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getRedirectUrl(): string
    {
        return '/admin';
    }
}