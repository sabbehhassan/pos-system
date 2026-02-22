<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class RecentSalesWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Sales';
    protected static ?int $sort = 4;

    protected function getTableQuery(): Builder|Relation|null
    {
        return Sale::query()
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('invoice_no')
                ->label('Invoice #')
                ->searchable(),

            Tables\Columns\TextColumn::make('total')
                ->label('Amount')
                ->money('PKR')
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('payment_method')
                ->label('Payment')
                ->colors([
                    'success' => 'cash',
                    'warning' => 'online',
                    'info'    => 'card',
                ]),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->since(),
        ];
    }
    public static function canView(): bool
{
    return auth()->user()->isAdmin()
        || auth()->user()->isCashier();
}
}