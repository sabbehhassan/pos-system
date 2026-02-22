<?php

namespace App\Filament\Shared\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class AdminSystemWidget extends Widget
{
    // ✅ NON-STATIC in Filament v3
    protected string $view = 'filament.admin.widgets.admin-system';

    protected int|string|array $columnSpan = 1;

public static function canView(): bool
{
    if (! auth()->check()) {
        return false;
    }

    $panelId = Filament::getCurrentPanel()?->getId();

    return match ($panelId) {
        'admin'   => auth()->user()->isAdmin(),
        'manager' => auth()->user()->isManager(),
        default   => false,
    };
}
}