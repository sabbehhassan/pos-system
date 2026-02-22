<?php

namespace App\Filament\Shared\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Sale;
use Carbon\Carbon;
use Filament\Facades\Filament;

class SalesChartWidget extends LineChartWidget
{
    // ✅ KEEP NON-STATIC
    protected ?string $heading = 'Last 7 Days Sales';

    // ✅ UNIQUE SORT (VERY IMPORTANT)
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $labels = [];
        $data   = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            $labels[] = $date->format('d M');

            $data[] = Sale::whereDate('created_at', $date)
                ->sum('total');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Sales (PKR)',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
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