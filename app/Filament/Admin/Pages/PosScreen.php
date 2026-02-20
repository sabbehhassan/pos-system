<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class PosScreen extends Page
{
    protected static ?string $navigationLabel = 'POS';

    protected static BackedEnum|string|null $navigationIcon = Heroicon::ShoppingCart; 
    protected static ?int $navigationSort = 10;


    public Collection $products;
    public array $cart = [];

    public string $search = '';
    public float $discountPercent = 0;

    // 🔵 PAYMENT FLOW STATES
    public bool $showPaymentModal = false;
    public bool $showInvoiceModal = false;

    public string $paymentMethod = 'cash';
    public ?Sale $lastSale = null;

    public function getView(): string
    {
        return 'filament.admin.pages.pos-screen';
    }

    public function mount(): void
    {
        $this->loadProducts();
    }

    public function updatedSearch(): void
    {
        $this->loadProducts();
    }

    protected function loadProducts(): void
    {
        $this->products = Product::where('stock', '>', 0)
            ->where('name', 'like', "%{$this->search}%")
            ->get();
    }

    /* ---------------- CART ---------------- */

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

    /* ---------------- CALCULATIONS ---------------- */

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)
            ->sum(fn ($item) => $item['price'] * $item['qty']);
    }

    public function getDiscountAmountProperty(): float
    {
        return ($this->subtotal * min($this->discountPercent, 100)) / 100;
    }

    public function getTotalProperty(): float
    {
        return max($this->subtotal - $this->discountAmount, 0);
    }

    /* ---------------- CHECKOUT FLOW ---------------- */

    // 🔵 STEP 1: Pay Now → Open Payment Modal
    public function openPayment(): void
    {
        if (empty($this->cart)) return;

        $this->showPaymentModal = true;
    }

    // 🔵 STEP 2: Done → Save Sale
    public function completePayment(): void
    {
        DB::transaction(function () {

            $sale = Sale::create([
                'user_id'          => Auth::id(),
                'invoice_no'       => 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
                'subtotal'         => $this->subtotal,
                'discount_percent' => $this->discountPercent,
                'discount_amount'  => $this->discountAmount,
                'total'            => $this->total,
                'payment_method'   => $this->paymentMethod,
                'status'           => 'paid',
            ]);

            foreach ($this->cart as $item) {
                SaleItem::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'qty'          => $item['qty'],
                    'price'        => $item['price'],
                    'total'        => $item['price'] * $item['qty'],
                ]);

                Product::where('id', $item['id'])
                    ->decrement('stock', $item['qty']);
            }

            $this->lastSale = $sale;
        });

        $this->showPaymentModal = false;
        $this->showInvoiceModal = true;

        $this->clearCart();
        $this->loadProducts();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->discountPercent = 0;
    }

    public function closeInvoice(): void
    {
        $this->showInvoiceModal = false;
        $this->lastSale = null;
    }
}