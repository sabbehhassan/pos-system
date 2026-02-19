<x-filament::page>
<div class="grid grid-cols-12 gap-6">

    <!-- PRODUCTS -->
    <div class="col-span-8 bg-white rounded-xl p-6">
        <h2 class="text-lg font-semibold mb-4">Products</h2>

        <div class="grid grid-cols-4 gap-4">
            @foreach ($products as $product)
                <div
                    wire:click="addToCart({{ $product->id }})"
                    class="cursor-pointer border rounded-xl p-4 hover:shadow-lg transition"
                >
                    <div class="h-24 bg-gray-200 rounded mb-2"></div>

                    <div class="font-medium text-sm">
                        {{ $product->name }}
                    </div>

                    <div class="text-primary-600 font-semibold text-sm">
                        Rs {{ number_format($product->price) }}
                    </div>

                    <div class="text-xs text-gray-400">
                        Stock: {{ $product->stock }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CART -->
    <div class="col-span-4 bg-white rounded-xl p-6 flex flex-col">
        <h2 class="text-lg font-semibold mb-4">Current Sale</h2>

        <div class="flex-1 overflow-auto border rounded-lg">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-1">Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($cart as $item)
                        <tr class="border-b">
                            <td class="px-2">{{ $item['name'] }}</td>
                            <td class="flex items-center gap-1">
                                <button wire:click="decreaseQty({{ $item['id'] }})">−</button>
                                {{ $item['qty'] }}
                                <button wire:click="increaseQty({{ $item['id'] }})">+</button>
                            </td>
                            <td>{{ $item['price'] }}</td>
                            <td>{{ $item['price'] * $item['qty'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-400 py-6">
                                Cart empty
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 space-y-2">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rs {{ number_format($this->subtotal) }}</span>
            </div>

            <div class="flex justify-between font-bold text-lg border-t pt-2">
                <span>Total</span>
                <span>Rs {{ number_format($this->subtotal) }}</span>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <button
                wire:click="clearCart"
                class="rounded-lg py-2 bg-gray-200"
            >
                Clear
            </button>

            <button
                wire:click="payNow"
                class="rounded-lg py-2 bg-primary-600 text-white"
            >
                Pay Now
            </button>
        </div>
    </div>

</div>
</x-filament::page>
