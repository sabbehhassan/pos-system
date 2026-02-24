<x-filament::page>

<div class="grid grid-cols-12 gap-6">

    <!-- ================= PRODUCTS ================= -->
    <div class="col-span-12 lg:col-span-8 bg-white rounded-xl p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h2 class="text-lg font-semibold">Products</h2>

            <input
                type="text"
                wire:model.live="search"
                placeholder="Search product..."
                class="w-full sm:w-64 rounded-lg border-gray-300 text-sm"
            >
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($products as $product)
                <div
                    wire:click="addToCart({{ $product->id }})"
                    class="cursor-pointer border rounded-xl p-4 hover:shadow-lg transition"
                >
                    <div class="h-24 bg-gray-200 rounded mb-3"></div>

                    <div class="font-medium text-sm leading-tight">
                        {{ $product->name }}
                    </div>

                    <div class="text-primary-600 font-semibold text-sm mt-1">
                        Rs {{ number_format($product->price) }}
                    </div>

                    <div class="text-xs text-gray-400">
                        Stock: {{ $product->stock }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ================= CURRENT SALE ================= -->
    <div class="col-span-12 lg:col-span-4 bg-white rounded-xl p-6 flex flex-col">
        <h2 class="text-lg font-semibold mb-4">Current Sale</h2>

        <!-- ===== CUSTOMER SECTION ===== -->
        <div class="mb-4 bg-gray-50 p-3 rounded">
            <div class="flex items-center justify-between mb-2">
                <strong>Customer</strong>

                <label class="flex items-center gap-2 text-sm">
                    <input type="checkbox" wire:model="walkInCustomer">
                    Walk-in
                </label>
            </div>

            <input
                type="text"
                wire:model.debounce.300ms="customerSearch"
                placeholder="Search customer name or phone"
                class="w-full rounded border-gray-300 text-sm"
                @if($walkInCustomer) disabled @endif
            >

            @if($customers && $customers->count())
                <div class="border rounded mt-1 bg-white max-h-40 overflow-y-auto">
                    @foreach($customers as $cust)
                        <div
                            wire:click="selectCustomer({{ $cust->id }})"
                            class="px-3 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                        >
                            {{ $cust->name }} ({{ $cust->phone }})
                        </div>
                    @endforeach
                </div>
            @endif

            @if($customerName)
                <div class="text-green-600 text-sm mt-1">
                    Selected: {{ $customerName }}
                </div>
            @endif
        </div>

        <!-- ===== CART ITEMS ===== -->
        <div class="flex-1 space-y-3 overflow-y-auto pr-1">
            @forelse ($cart as $item)
                <div class="border rounded-lg p-3 flex justify-between items-center">
                    <div class="flex-1">
                        <div class="text-sm font-medium">{{ $item['name'] }}</div>
                        <div class="text-xs text-gray-400">
                            Rs {{ number_format($item['price']) }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            wire:click="decreaseQty({{ $item['id'] }})"
                            class="w-7 h-7 bg-gray-200 rounded"
                        >−</button>

                        <span class="w-5 text-center text-sm">
                            {{ $item['qty'] }}
                        </span>

                        <button
                            wire:click="increaseQty({{ $item['id'] }})"
                            class="w-7 h-7 bg-gray-200 rounded"
                        >+</button>
                    </div>

                    <div class="w-20 text-right text-sm font-semibold">
                        Rs {{ number_format($item['price'] * $item['qty']) }}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-400 py-10">
                    Cart is empty
                </div>
            @endforelse
        </div>

        <!-- ===== TOTALS ===== -->
        <div class="mt-4 space-y-3 border-t pt-4 text-sm">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rs {{ number_format($this->subtotal) }}</span>
            </div>

            <div>
                <label class="text-xs text-gray-500">% Discount</label>
                <input
                    type="number"
                    min="0"
                    max="100"
                    wire:model.live="discountPercent"
                    class="mt-1 w-full rounded-lg border-gray-300 text-sm"
                >
            </div>

            <div class="flex justify-between text-gray-500">
                <span>Discount</span>
                <span>- Rs {{ number_format($this->discountAmount) }}</span>
            </div>

            <div class="flex justify-between text-lg font-bold border-t pt-2">
                <span>Total</span>
                <span>Rs {{ number_format($this->total) }}</span>
            </div>
        </div>

        <!-- ===== ACTIONS ===== -->
        <div class="mt-4 grid grid-cols-2 gap-3">
            <button
                wire:click="clearCart"
                class="rounded-lg py-2 bg-gray-200 hover:bg-gray-300"
            >
                Clear
            </button>

            <button
                wire:click="openPayment"
                class="rounded-lg py-2 bg-primary-600 text-white hover:bg-primary-700"
            >
                Pay Now
            </button>
        </div>
    </div>

</div>

<!-- ================= PAYMENT MODAL ================= -->
@if($showPaymentModal)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white w-full max-w-md rounded-xl p-6 space-y-4">
        <h2 class="text-lg font-bold">Payment Method</h2>

        <div class="space-y-2 text-sm">
            <label class="flex items-center gap-2">
                <input type="radio" wire:model="paymentMethod" value="cash"> Cash
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" wire:model="paymentMethod" value="online"> Online
            </label>
            <label class="flex items-center gap-2">
                <input type="radio" wire:model="paymentMethod" value="card"> Card Swipe
            </label>
        </div>

        <div class="text-right font-bold text-lg">
            Total: Rs {{ number_format($this->total) }}
        </div>

        <div class="flex gap-3">
            <button
                wire:click="completePayment"
                class="flex-1 bg-green-600 text-white py-2 rounded-lg"
            >
                Done
            </button>

            <button
                wire:click="$set('showPaymentModal', false)"
                class="flex-1 bg-gray-300 py-2 rounded-lg"
            >
                Cancel
            </button>
        </div>
    </div>
</div>
@endif

<!-- ================= INVOICE MODAL ================= -->
@if($showInvoiceModal && $lastSale)
<div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div id="invoice-area" class="bg-white w-full max-w-sm rounded-xl p-6 text-sm">
        <h2 class="text-center text-lg font-bold mb-2">INVOICE</h2>

        <p><strong>Invoice:</strong> {{ $lastSale->invoice_no }}</p>
        <p><strong>Customer:</strong> {{ $lastSale->customer_name }}</p>
        <p><strong>Payment:</strong> {{ strtoupper($lastSale->payment_method) }}</p>
        <p><strong>Date:</strong> {{ $lastSale->created_at->format('d M Y H:i') }}</p>

        <hr class="my-2">

        @foreach($lastSale->items as $item)
            <div class="flex justify-between">
                <span>{{ $item->product_name }} x{{ $item->qty }}</span>
                <span>Rs {{ number_format($item->total) }}</span>
            </div>
        @endforeach

        <hr class="my-2">

        <div class="flex justify-between font-bold">
            <span>Total</span>
            <span>Rs {{ number_format($lastSale->total) }}</span>
        </div>

        <div class="flex gap-3 mt-4">
            <button
                onclick="window.print()"
                class="flex-1 bg-primary-600 text-white py-2 rounded"
            >
                Print
            </button>

            <button
                wire:click="closeInvoice"
                class="flex-1 bg-gray-300 py-2 rounded"
            >
                Close
            </button>
        </div>
    </div>
</div>
@endif

</x-filament::page>