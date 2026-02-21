<?php

namespace App\Filament\Admin\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Sale;
use Carbon\Carbon;

class SalesChartWidget extends LineChartWidget
{
    // ✅ NON-static (this is the fix)
    protected ?string $heading = 'Last 7 Days Sales';

    protected static ?int $sort = 3;

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
                    'data' => $data,
                    'borderColor' => '#f97316',
                    'backgroundColor' => 'rgba(249,115,22,0.3)',
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }
}