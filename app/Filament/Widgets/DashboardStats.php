<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        // 📅 Today Sales
        $todaySales = Sale::whereDate('created_at', Carbon::today())
            ->sum('total');

        // 📦 Inventory Value (stock × selling price)
        $inventoryValue = Product::sum(
            DB::raw('stock * price')
        );

        // ⚠️ Low Stock Items
        $lowStockCount = Product::where('stock', '<=', 30)->count();

        // ⭐ Best Selling Product
        $bestProduct = SaleItem::selectRaw('product_name, SUM(qty) as total_qty')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->first();

        return [
            Stat::make('Today Sales', 'PKR ' . number_format($todaySales, 2))
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('Inventory Value', 'PKR ' . number_format($inventoryValue, 2))
                ->icon('heroicon-o-cube')
                ->color('info'),

            Stat::make('Low Stock Items', $lowStockCount)
                ->icon('heroicon-o-exclamation-triangle')
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make(
                'Best Selling Product',
                $bestProduct
                    ? $bestProduct->product_name . ' (' . $bestProduct->total_qty . ')'
                    : 'N/A'
            )
                ->icon('heroicon-o-star')
                ->color('warning'),
        ];
    }
}