<x-filament-panels::page>

    <div class="space-y-8">

        {{-- ===== FILTERS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="text-sm font-medium">From Date</label>
                <input type="date" wire:model="fromDate" class="w-full rounded border-gray-300">
            </div>

            <div>
                <label class="text-sm font-medium">To Date</label>
                <input type="date" wire:model="toDate" class="w-full rounded border-gray-300">
            </div>

            <div class="flex items-end">
                <button wire:click="loadReport"
                        class="px-4 py-2 bg-primary-600 text-white rounded">
                    Generate Report
                </button>
            </div>
        </div>

        {{-- ===== STATS ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="p-4 bg-white rounded shadow">
                <div class="text-sm text-gray-500">Total Sales</div>
                <div class="text-xl font-bold">PKR {{ number_format($totalSales, 2) }}</div>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="text-sm text-gray-500">Invoices</div>
                <div class="text-xl font-bold">{{ $totalInvoices }}</div>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="text-sm text-gray-500">Cash</div>
                <div class="text-xl font-bold">PKR {{ number_format($cashSales, 2) }}</div>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="text-sm text-gray-500">Online</div>
                <div class="text-xl font-bold">PKR {{ number_format($onlineSales, 2) }}</div>
            </div>

            <div class="p-4 bg-white rounded shadow">
                <div class="text-sm text-gray-500">Card</div>
                <div class="text-xl font-bold">PKR {{ number_format($cardSales, 2) }}</div>
            </div>
        </div>

        {{-- ===== DAILY SALES GRAPH ===== --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Daily Sales Graph</h3>
            <div wire:ignore style="height:260px;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        {{-- ===== TOP PRODUCTS GRAPH ===== --}}
@if(count($topProductLabels))
    <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">Top Products (By Quantity)</h3>

        <div wire:ignore style="height:1px;">
            <canvas id="topProductsChart"></canvas>
        </div>
    </div>
@endif

        {{-- ===== TOP PRODUCTS TABLE ===== --}}
        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Top Products Table</h3>

            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 border">#</th>
                        <th class="p-2 border">Product</th>
                        <th class="p-2 border text-right">Qty</th>
                        <th class="p-2 border text-right">Revenue (PKR)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProductsTable as $index => $row)
                        <tr>
                            <td class="p-2 border">{{ $index + 1 }}</td>
                            <td class="p-2 border">{{ $row['product_name'] }}</td>
                            <td class="p-2 border text-right">{{ $row['total_qty'] }}</td>
                            <td class="p-2 border text-right">
                                {{ number_format($row['revenue'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-4 text-center text-gray-500">
                                No data found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- ===== CHARTS ===== --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let dailyChart = null;
        let topProductsChart = null;

        function renderDailySalesChart(labels, totals) {
            const ctx = document.getElementById('dailySalesChart');
            if (!ctx) return;
            if (dailyChart) dailyChart.destroy();

            dailyChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales (PKR)',
                        data: totals,
                        borderColor: '#f97316',
                        backgroundColor: 'rgba(249,115,22,0.15)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        function renderTopProductsChart(labels, qty) {
    if (!labels.length || !qty.length) return;

    const canvas = document.getElementById('topProductsChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // 🔥 IMPORTANT: reverse for correct visual order
    const fixedLabels = [...labels].reverse();
    const fixedQty = [...qty].reverse();

    if (topProductsChart) {
        topProductsChart.destroy();
    }

    topProductsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: fixedLabels,
            datasets: [{
                label: 'Qty Sold',
                data: fixedQty,
                backgroundColor: '#22c55e',
                borderRadius: 6,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
}

        document.addEventListener('livewire:load', () => {
            renderDailySalesChart(@json($dailyLabels), @json($dailyTotals));
            renderTopProductsChart(@json($topProductLabels), @json($topProductQty));
        });

        Livewire.on('refreshChart', () => {
            renderDailySalesChart(@json($dailyLabels), @json($dailyTotals));
        });

        Livewire.on('refreshTopProductsChart', () => {
            renderTopProductsChart(@json($topProductLabels), @json($topProductQty));
        });
    </script>

</x-filament-panels::page>