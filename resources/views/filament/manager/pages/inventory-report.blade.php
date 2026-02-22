<x-filament-panels::page>

<div class="space-y-6">

    {{-- ===== SUMMARY ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Products</div>
            <div class="text-xl font-bold">{{ $totalProducts }}</div>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Total Stock Qty</div>
            <div class="text-xl font-bold">{{ $totalStockQty }}</div>
        </div>

        <div class="p-4 bg-white rounded shadow">
            <div class="text-sm text-gray-500">Inventory Value</div>
            <div class="text-xl font-bold">
                PKR {{ number_format($inventoryValue, 2) }}
            </div>
        </div>
    </div>

    {{-- ===== CURRENT STOCK TABLE ===== --}}
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Current Stock</h3>

        <table class="w-full border text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Product</th>
                    <th class="p-2 border">Category</th>
                    <th class="p-2 border text-right">Qty</th>
                    <th class="p-2 border text-right">Cost (PKR)</th>
                    <th class="p-2 border text-right">Value (PKR)</th>
                    <th class="p-2 border text-center">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($products as $index => $product)
                    @php
                        $qty   = (int) $product->stock;
                        $cost  = (float) $product->price;
                        $value = $qty * $cost;
                    @endphp

                    <tr>
                        <td class="p-2 border">{{ $index + 1 }}</td>
                        <td class="p-2 border">{{ $product->name }}</td>
                        <td class="p-2 border">{{ $product->category?->name ?? '-' }}</td>

                        <td class="p-2 border text-right">{{ $qty }}</td>

                        <td class="p-2 border text-right">
                            PKR {{ number_format($cost, 2) }}
                        </td>

                        <td class="p-2 border text-right">
                            PKR {{ number_format($value, 2) }}
                        </td>

                        <td class="p-2 border text-center">
                            @if($qty <= 30)
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">
                                    Low
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">
                                    OK
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

</x-filament-panels::page>

