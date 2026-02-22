<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class AdminSystemWidget extends Widget
{
    // ✅ NON-STATIC in Filament v3
    protected string $view = 'filament.admin.widgets.admin-system';

    protected int|string|array $columnSpan = 1;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}