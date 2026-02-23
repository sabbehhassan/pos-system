<x-filament::page>
    <x-filament::card>
        <table class="w-full text-sm border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">#</th>
                    <th class="p-2 border">Name</th>
                    <th class="p-2 border">Phone</th>
                    <th class="p-2 border">Shop Name</th>
                    <th class="p-2 border">Shop Address</th>
                    <th class="p-2 border">Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->customers as $customer)
                    <tr>
                        <td class="p-2 border">{{ $loop->iteration }}</td>
                        <td class="p-2 border">{{ $customer->name }}</td>
                        <td class="p-2 border">{{ $customer->phone }}</td>
                        <td class="p-2 border">{{ $customer->shop_name ?? '-' }}</td>
                        <td class="p-2 border">{{ $customer->shop_address ?? '-' }}</td>
                        <td class="p-2 border">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">
                            No customers found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-filament::card>
</x-filament::page>