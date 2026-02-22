<?php

namespace App\Filament\Shared\Widgets;
use Filament\Facades\Filament;
use Filament\Widgets\Widget;

class CashierSummaryWidget extends Widget
{
    // ✅ Filament v3: NON-STATIC
    protected string $view = 'filament.admin.widgets.cashier-summary';

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