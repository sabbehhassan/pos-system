<?php

namespace App\Filament\Shared\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Facades\Filament;
use App\Models\Sale;
use App\Models\Product;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    // 🔁 Optional real-time (safe)
    protected function getPollingInterval(): ?string
    {
        return null; // turn OFF for now (stable)
    }

    protected function getStats(): array
    {
        // ✅ SAME LOGIC AS OLD (THIS WORKS)
        $todaySales = Sale::whereDate('created_at', Carbon::today())
            ->sum('total');

        $inventoryValue = Product::sum(
            DB::raw('stock * price')
        );

        $lowStockCount = Product::where('stock', '<=', 30)->count();

        $bestProduct = SaleItem::selectRaw('product_name, SUM(qty) as total_qty')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->first();

        return [
            Stat::make('Today Sales', 'PKR ' . number_format($todaySales, 2))
                ->color('success'),

            Stat::make('Inventory Value', 'PKR ' . number_format($inventoryValue, 2))
                ->color('info'),

            Stat::make('Low Stock Items', $lowStockCount)
                ->color($lowStockCount > 0 ? 'danger' : 'success'),

            Stat::make(
                'Best Selling Product',
                $bestProduct
                    ? $bestProduct->product_name . ' (' . $bestProduct->total_qty . ')'
                    : 'N/A'
            )
                ->color('warning'),
        ];
    }

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