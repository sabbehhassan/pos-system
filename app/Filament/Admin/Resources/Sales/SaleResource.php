<?php

namespace App\Filament\Admin\Resources\Sales;

use App\Models\Sale;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use App\Filament\Admin\Resources\Sales\Pages;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;
    protected static ?string $navigationLabel = 'Sales';
    protected static ?int $navigationSort = 20;

    /* ================= ICON (SAFE) ================= */
    public static function getNavigationIcon(): BackedEnum|string|null
    {
        return Heroicon::ReceiptRefund;
    }

    /* ================= TABLE ================= */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_no')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Cashier')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Payment')
                    ->colors([
                        'success' => 'cash',
                        'warning' => 'online',
                        'info'    => 'card',
                    ]),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('PKR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('customer_name')
                ->label('Customer')
                ->searchable()
                ->sortable()
                ->default('-'),
                  ])
            ->defaultSort('created_at', 'desc');
               }

    /* ================= PAGES ================= */
          public static function getPages(): array
              {
             return [
        'index'  => Pages\ListSales::route('/'),
        'create' => Pages\CreateSale::route('/create'),
        'edit'   => Pages\EditSale::route('/{record}/edit'),
        'view'   => Pages\ViewSale::route('/{record}'),

        // 🖨 PRINT ROUTE (THIS LINE IS CRITICAL)
        'print'  => Pages\PrintSale::route('/{record}/print'),
    ];
}
}