<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    // ✅ Filament v3: NON-STATIC
    protected string $view = 'filament.admin.widgets.welcome-widget';

    protected int|string|array $columnSpan = 'full';

    // 👇 sab roles ke liye
    public static function canView(): bool
    {
        return auth()->check();
    }
}