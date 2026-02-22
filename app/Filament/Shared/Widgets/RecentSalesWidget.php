<?php

namespace App\Filament\Shared\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Facades\Filament;

class RecentSalesWidget extends TableWidget
{
    protected static ?string $heading = 'Recent Sales';

    // ✅ UNIQUE SORT
    protected static ?int $sort = 4;

    protected function getTableQuery(): Builder
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
                ->formatStateUsing(fn ($state) => 'PKR ' . number_format($state))
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('payment_method')
                ->label('Payment')
                ->color(fn (string $state) => match ($state) {
                    'cash'   => 'success',
                    'online' => 'warning',
                    'card'   => 'info',
                    default  => 'gray',
                }),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->since(),
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