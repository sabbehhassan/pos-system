<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use Illuminate\Database\Eloquent\Collection;

class PosScreen extends Page
{
    protected static ?string $navigationLabel = 'POS';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::ShoppingCart;

    protected static ?int $navigationSort = 10;

    /** ----------------------------
     *  STATE (IMPORTANT FIX)
     *  ---------------------------- */
    public Collection $products;   // ✅ NOT array
    public array $cart = [];       // cart array hi rahegi

    /** ----------------------------
     *  VIEW
     *  ---------------------------- */
    public function getView(): string
    {
        return 'filament.admin.pages.pos-screen';
    }

    /** ----------------------------
     *  LIFECYCLE
     *  ---------------------------- */
    public function mount(): void
    {
        $this->products = Product::where('stock', '>', 0)->get();
    }

    /** ----------------------------
     *  CART LOGIC
     *  ---------------------------- */
    public function addToCart(int $productId): void
    {
        $product = Product::findOrFail($productId);

        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] < $product->stock) {
                $this->cart[$productId]['qty']++;
            }
        } else {
            $this->cart[$productId] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->price,
                'qty'   => 1,
            ];
        }
    }

    public function increaseQty(int $id): void
    {
        $product = Product::findOrFail($id);

        if ($this->cart[$id]['qty'] < $product->stock) {
            $this->cart[$id]['qty']++;
        }
    }

    public function decreaseQty(int $id): void
    {
        if ($this->cart[$id]['qty'] > 1) {
            $this->cart[$id]['qty']--;
        } else {
            unset($this->cart[$id]);
        }
    }

    public function getSubtotalProperty(): int
    {
        return collect($this->cart)
            ->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    /** ----------------------------
     *  CHECKOUT
     *  ---------------------------- */
    public function clearCart(): void
    {
        $this->cart = [];
    }

    public function payNow(): void
    {
        if (empty($this->cart)) {
            return;
        }

        DB::transaction(function () {
            foreach ($this->cart as $item) {
                Product::where('id', $item['id'])
                    ->decrement('stock', $item['qty']);
            }
        });

        $this->clearCart();
        $this->mount(); // reload products
    }
}
