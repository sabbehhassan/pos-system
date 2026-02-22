<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Shared\Widgets\{
    WelcomeWidget,
    DashboardStats,
    SalesChartWidget,
    LowStockWidget,
    RecentSalesWidget
};

class Dashboard extends BaseDashboard
{
    public function getHeaderWidgets(): array
    {
        return [
            WelcomeWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            DashboardStats::class,
            SalesChartWidget::class,
            LowStockWidget::class,
            RecentSalesWidget::class,
        ];
    }
}