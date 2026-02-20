<x-filament::page>
<div class="grid grid-cols-12 gap-6">

    <!-- PRODUCTS -->
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

    <!-- CURRENT SALE -->
    <div class="col-span-12 lg:col-span-4 bg-white rounded-xl p-6 flex flex-col">
        <h2 class="text-lg font-semibold mb-4">Current Sale</h2>

        <!-- CART ITEMS -->
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

        <!-- TOTALS -->
        <div class="mt-4 space-y-3 border-t pt-4 text-sm">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rs {{ number_format($this->subtotal) }}</span>
            </div>

            <!-- % DISCOUNT -->
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

        <!-- ACTIONS -->
        <div class="mt-4 grid grid-cols-2 gap-3">
            <button
                wire:click="clearCart"
                class="rounded-lg py-2 bg-gray-200 hover:bg-gray-300"
            >
                Clear
            </button>

            <button
                wire:click="payNow"
                class="rounded-lg py-2 bg-primary-600 text-white hover:bg-primary-700"
            >
                Pay Now
            </button>
        </div>
    </div>

</div>
</x-filament::page>
