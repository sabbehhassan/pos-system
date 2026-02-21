<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class LowStockWidget extends TableWidget
{
    protected static ?string $heading = 'Low Stock Items';
    protected static ?int $sort = 3;

    protected function getTableQuery(): Builder|Relation|null
    {
        return Product::query()
            ->where('stock', '<=', 30)
            ->orderBy('stock', 'asc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Product')
                ->searchable(),

            Tables\Columns\TextColumn::make('stock')
                ->label('Qty')
                ->color(fn ($state) => $state <= 10 ? 'danger' : 'warning')
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->getStateUsing(fn ($record) =>
                    $record->stock <= 10 ? 'Critical' : 'Low'
                )
                ->colors([
                    'danger' => 'Critical',
                    'warning' => 'Low',
                ]),
        ];
    }
}