<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class CashierSummaryWidget extends Widget
{
    // ✅ Filament v3: NON-STATIC
    protected string $view = 'filament.admin.widgets.cashier-summary';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isCashier();
    }
}