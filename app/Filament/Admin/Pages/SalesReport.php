<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Support\Facades\DB;

class SalesReport extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::ChartBar;
    protected static ?string $navigationLabel = 'Sales Report';

    // Filament v3 (NON-static)
    protected string $view = 'filament.admin.pages.sales-report';

    public static function getNavigationGroup(): string
    {
        return 'Reports';
    }

    /* ===== FILTERS ===== */
    public $fromDate;
    public $toDate;

    /* ===== STATS ===== */
    public $totalSales = 0;
    public $totalInvoices = 0;
    public $cashSales = 0;
    public $onlineSales = 0;
    public $cardSales = 0;

    /* ===== DAILY SALES GRAPH ===== */
    public array $dailyLabels = [];
    public array $dailyTotals = [];

    /* ===== TOP PRODUCTS ===== */
    public array $topProductLabels = [];
    public array $topProductQty = [];
    public array $topProductsTable = [];

    public function mount(): void
    {
        $this->fromDate = now()->startOfMonth()->toDateString();
        $this->toDate   = now()->toDateString();

        $this->loadReport();
    }

    public function loadReport(): void
    {
        $from = Carbon::parse($this->fromDate)->startOfDay();
        $to   = Carbon::parse($this->toDate)->endOfDay();

        $baseQuery = Sale::whereBetween('created_at', [$from, $to]);

        /* ===== TOTALS ===== */
        $this->totalSales = $baseQuery->sum('total');
        $this->totalInvoices = $baseQuery->count();

        $this->cashSales = (clone $baseQuery)->where('payment_method', 'cash')->sum('total');
        $this->onlineSales = (clone $baseQuery)->where('payment_method', 'online')->sum('total');
        $this->cardSales = (clone $baseQuery)->where('payment_method', 'card')->sum('total');

        /* ===== DAILY SALES GRAPH ===== */
        $dailySales = Sale::selectRaw('DATE(created_at) as day, SUM(total) as total')
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $this->dailyLabels = $dailySales->pluck('day')->toArray();
        $this->dailyTotals = $dailySales->pluck('total')->toArray();

        /* ===== TOP PRODUCTS ===== */
        $topProducts = SaleItem::select(
                'product_name',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(total) as revenue')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $this->topProductLabels = $topProducts->pluck('product_name')->toArray();
        $this->topProductQty = $topProducts->pluck('total_qty')->toArray();
        $this->topProductsTable = $topProducts->toArray();

        $this->dispatch('refreshChart');
        $this->dispatch('refreshTopProductsChart');
    }
}