<?php

namespace App\Filament\Shared\Widgets;

use Filament\Facades\Filament;
use App\Models\Product;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LowStockWidget extends TableWidget
{
    protected static ?string $heading = 'Low Stock Items';

    // ✅ UNIQUE & SAFE SORT
    protected static ?int $sort = 3;

    protected function getTableQuery(): Builder
    {
        return Product::query()
            ->where('stock', '<=', 30)
            ->orderBy('stock');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Product')
                ->searchable(),

            Tables\Columns\TextColumn::make('stock')
                ->label('Qty')
                ->weight('bold')
                ->color(fn (int $state) => $state <= 10 ? 'danger' : 'warning'),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn ($record) =>
                    $record->stock <= 10 ? 'Critical' : 'Low'
                )
                ->color(fn (string $state) =>
                    $state === 'Critical' ? 'danger' : 'warning'
                ),
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