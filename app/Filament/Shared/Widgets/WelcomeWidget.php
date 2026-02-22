<?php

namespace App\Filament\Shared\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    protected string $view = 'filament.shared.widgets.welcome-widget';

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        if (! auth()->check()) {
            return false;
        }

        return in_array(
            Filament::getCurrentPanel()?->getId(),
            ['admin', 'manager']
        );
    }
}