<?php

namespace App\Filament\Shared\Pages;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class PosScreen extends Page
{
    // ✅ Filament v3: view
    protected string $view = 'filament.admin.pages.pos-screen';

    /* ================= SIDEBAR CONFIG (FINAL & CORRECT) ================= */

public static function getNavigationLabel(): string
{
    return 'POS';
}

public static function getNavigationIcon(): string
{
    return 'heroicon-o-shopping-cart';
}

public static function getNavigationSort(): int
{
    return 10;
}

    /* ================= CUSTOMER ================= */

    public bool $walkInCustomer = false;
    public string $customerSearch = '';
    public ?int $customerId = null;
    public ?string $customerName = null;
    public Collection $customers;

    /* ================= PRODUCTS / CART ================= */

    public Collection $products;
    public array $cart = [];
    public string $search = '';
    public $discountPercent = 0;

    /* ================= PAYMENT ================= */

    public bool $showPaymentModal = false;
    public bool $showInvoiceModal = false;
    public string $paymentMethod = 'cash';
    public ?Sale $lastSale = null;

    public function mount(): void
    {
        $this->loadProducts();
        $this->customers = collect();
    }
    public static function canAccess(): bool
    {
    return in_array(auth()->user()?->role, ['admin', 'cashier']);
    }

    /* ================= CUSTOMER LOGIC ================= */

    public function updatedCustomerSearch(): void
    {
        if ($this->walkInCustomer || strlen($this->customerSearch) < 2) {
            $this->customers = new Collection();
            return;
        }

        $this->customers = Customer::where('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('phone', 'like', "%{$this->customerSearch}%")
            ->limit(10)
            ->get();
    }

    public function selectCustomer(int $id): void
    {
        $customer = Customer::find($id);
        if (! $customer) return;

        $this->customerId = $customer->id;
        $this->customerName = $customer->name;
        $this->customerSearch = $customer->name;
        $this->customers = new Collection();
    }

    public function updatedWalkInCustomer(): void
    {
        if ($this->walkInCustomer) {
            $this->customerId = null;
            $this->customerName = 'Walk-in Customer';
            $this->customerSearch = '';
            $this->customers = new Collection();
        } else {
            $this->customerName = null;
        }
    }

    /* ================= PRODUCTS ================= */

    protected function loadProducts(): void
    {
        $this->products = Product::where('stock', '>', 0)
            ->where('name', 'like', "%{$this->search}%")
            ->get();
    }

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
        if ($this->cart[$id]['qty'] < Product::find($id)->stock) {
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

    /* ================= TOTALS ================= */

    public function getSubtotalProperty(): float
    {
        return collect($this->cart)->sum(fn ($i) => $i['price'] * $i['qty']);
    }

    public function getDiscountAmountProperty(): float
    {
    $percent = (float) $this->discountPercent;

    return ($this->subtotal * min($percent, 100)) / 100;
    }

    public function getTotalProperty(): float
    {
        return max($this->subtotal - $this->discountAmount, 0);
    }

    /* ================= CHECKOUT ================= */

    public function openPayment(): void
    {
        if (! empty($this->cart)) {
            $this->showPaymentModal = true;
        }
    }

    public function completePayment(): void
    {
        DB::transaction(function () {

            $sale = Sale::create([
                'user_id'       => Auth::id(),
                'customer_id'   => $this->customerId,
                'customer_name' => $this->walkInCustomer
                    ? 'Walk-in Customer'
                    : $this->customerName,

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

                Product::where('id', $item['id'])->decrement('stock', $item['qty']);
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