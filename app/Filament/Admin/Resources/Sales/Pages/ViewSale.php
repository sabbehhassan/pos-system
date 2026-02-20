<?php

namespace App\Filament\Admin\Resources\Sales\Pages;

use App\Filament\Admin\Resources\Sales\SaleResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Actions\Action;

class ViewSale extends ViewRecord
{
    protected static string $resource = SaleResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([

            Section::make('Invoice Details')
                ->schema([
                    Grid::make(3)->schema([

                        Text::make('Invoice #')
                            ->content(fn ($record) => $record->invoice_no),

                        Text::make('Date')
                            ->content(fn ($record) =>
                                $record->created_at?->format('d M Y H:i')
                            ),

                        Text::make('Cashier')
                            ->content(fn ($record) =>
                                $record->user?->name ?? '-'
                            ),

                        Text::make('Payment Method')
                            ->content(fn ($record) =>
                                ucfirst($record->payment_method)
                            ),

                        Text::make('Status')
                            ->content(fn ($record) =>
                                ucfirst($record->status)
                            ),

                        Text::make('Total')
                            ->content(fn ($record) =>
                                'PKR ' . number_format($record->total, 2)
                            ),
                    ]),
                ]),

            Section::make('Items')
                ->schema([
                    Text::make('Sale Items')
                        ->content(fn ($record) =>
                            $record->items->count()
                                ? $record->items
                                    ->map(fn ($item) =>
                                        "{$item->product_name} × {$item->qty} = PKR " .
                                        number_format($item->total, 2)
                                    )
                                    ->join(' | ')
                                : 'No items found'
                        ),
                ]),
        ]);
    }
    protected function getHeaderActions(): array
{
    return [
        Action::make('print')
            ->label('Print Invoice')
            ->icon('heroicon-o-printer')
            ->url(fn () => SaleResource::getUrl('print', [
                'record' => $this->record,
            ]))
            ->openUrlInNewTab(),
    ];
}
}