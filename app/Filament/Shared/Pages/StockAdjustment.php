<?php

namespace App\Filament\Shared\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\StockAdjustmentLog;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustment extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::ArrowsUpDown;
    protected static ?string $navigationLabel = 'Stock Adjustment';

    protected string $view = 'filament.admin.pages.stock-adjustment';

    public static function getNavigationGroup(): string
    {
        return 'Inventory';
    }

    /* ===== FORM FIELDS ===== */
    public ?int $product_id = null;
    public int $qty = 1;
    public string $type = 'in';
    public ?string $reason = null;

    /* ===== FORM SCHEMA ===== */
    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->label('Product')
                ->options(Product::pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\Select::make('type')
                ->label('Adjustment Type')
                ->options([
                    'in'  => 'Add Stock',
                    'out' => 'Remove Stock',
                ])
                ->required(),

            Forms\Components\TextInput::make('qty')
                ->label('Quantity')
                ->numeric()
                ->minValue(1)
                ->required(),

            Forms\Components\Textarea::make('reason')
                ->label('Reason')
                ->rows(3)
                ->placeholder('New stock / Damage / Return / Adjustment'),
        ];
    }

    /* ===== SAVE STOCK ===== */
    public function submit(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'qty'        => 'required|integer|min:1',
            'type'       => 'required|in:in,out',
            'reason'     => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {

            $product = Product::lockForUpdate()->findOrFail($this->product_id);

            // ❌ Prevent negative stock
            if ($this->type === 'out' && $product->stock < $this->qty) {
                Notification::make()
                    ->title('Insufficient stock')
                    ->danger()
                    ->send();

                throw new \Exception('Insufficient stock');
            }

            // 🔄 UPDATE PRODUCT STOCK (ALTER products table)
            if ($this->type === 'in') {
                $product->stock += $this->qty;
            } else {
                $product->stock -= $this->qty;
            }

            $product->save(); // 🔥 ACTUAL DB UPDATE HERE

            // 🧾 SAVE ADJUSTMENT HISTORY
            StockAdjustmentLog::create([
                'product_id' => $product->id,
                'qty'        => $this->qty,
                'type'       => $this->type,
                'reason'     => $this->reason,
                'created_by'=> Auth::id(),
            ]);
        });

        // ✅ SUCCESS MESSAGE
        Notification::make()
            ->title('Stock updated successfully')
            ->success()
            ->send();

        // 🔄 RESET FORM
        $this->reset(['product_id', 'qty', 'type', 'reason']);
    }
}