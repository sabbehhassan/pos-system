<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\PriceAdjustmentLog;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PriceAdjustment extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static BackedEnum|string|null $navigationIcon = Heroicon::CurrencyDollar;
    protected static ?string $navigationLabel = 'Price Adjustment';

    protected string $view = 'filament.admin.pages.price-adjustment';

    public static function getNavigationGroup(): string
    {
        return 'Inventory';
    }

    /* ===== FORM FIELDS ===== */
    public ?int $product_id = null;
    public float $new_price = 0;
    public ?string $reason = null;

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('product_id')
                ->label('Product')
                ->options(Product::pluck('name', 'id'))
                ->searchable()
                ->required(),

            Forms\Components\TextInput::make('new_price')
                ->label('New Price (PKR)')
                ->numeric()
                ->minValue(0)
                ->required(),

            Forms\Components\Textarea::make('reason')
                ->label('Reason')
                ->rows(3)
                ->placeholder('Supplier price change / Market update'),
        ];
    }

    public function submit(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'new_price'  => 'required|numeric|min:0',
            'reason'     => 'nullable|string|max:255',
        ]);

        DB::transaction(function () {

            $product = Product::lockForUpdate()->findOrFail($this->product_id);

            $oldPrice = $product->price;

            // 🔄 UPDATE PRODUCT PRICE
            $product->update([
                'price' => $this->new_price,
            ]);

            // 🧾 SAVE PRICE HISTORY
            PriceAdjustmentLog::create([
                'product_id' => $product->id,
                'old_price'  => $oldPrice,
                'new_price'  => $this->new_price,
                'reason'     => $this->reason,
                'created_by' => Auth::id(),
            ]);
        });

        Notification::make()
            ->title('Price updated successfully')
            ->success()
            ->send();

        $this->reset(['product_id', 'new_price', 'reason']);
    }
}