<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\Widget;

class WelcomeWidget extends Widget
{
    // ❌ static NAHI
    protected string $view = 'filament.admin.widgets.welcome-widget';

    protected int|string|array $columnSpan = 'full';
}