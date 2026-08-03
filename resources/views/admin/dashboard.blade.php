<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - MEETA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
</head>
<body class="bg-[#0F172A] text-white min-h-screen">

    <div class="flex">
        <!-- Sidebar -->
        <aside class="w-64 min-h-screen bg-[#111827] border-r border-gray-800 p-6">
            <h1 class="text-xl font-bold text-[#F97316] mb-8">MEETA Admin</h1>
            <nav class="space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded bg-[#F97316]/10 text-[#F97316] font-medium">Dashboard</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-400 hover:bg-gray-800">Vendors</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-400 hover:bg-gray-800">Products</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-400 hover:bg-gray-800">Orders</a>
                <a href="#" class="block px-3 py-2 rounded text-gray-400 hover:bg-gray-800">Payouts</a>
            </nav>
        </aside>

        <!-- Main content -->
        <main class="flex-1 p-8">
            <h2 class="text-2xl font-bold mb-6">Dashboard</h2>

            <!-- Stat cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="text-gray-400 text-sm">Total Revenue</p>
                    <p class="text-2xl font-bold mt-1">₹{{ number_format($totalRevenue, 2) }}</p>
                </div>
                <div class="bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="text-gray-400 text-sm">Active Vendors</p>
                    <p class="text-2xl font-bold mt-1">{{ $activeVendors }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $pendingVendors }} pending approval</p>
                </div>
                <div class="bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="text-gray-400 text-sm">Total Orders</p>
                    <p class="text-2xl font-bold mt-1">{{ $totalOrders }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $todayOrders }} today</p>
                </div>
                <div class="bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="text-gray-400 text-sm">Pending Product Approvals</p>
                    <p class="text-2xl font-bold mt-1">{{ $pendingProducts }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Chart -->
                <div class="lg:col-span-2 bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="font-semibold mb-4">Revenue (Last 7 Days)</p>
                    <canvas id="revenueChart" height="120"></canvas>
                </div>

                <!-- Recent orders -->
                <div class="bg-[#1E293B] rounded-lg p-5 border border-gray-800">
                    <p class="font-semibold mb-4">Recent Orders</p>
                    <div class="space-y-3">
                        @forelse ($recentOrders as $order)
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm font-medium">{{ $order->user->name ?? 'Guest' }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->order_number }}</p>
                                </div>
                                <p class="text-sm font-semibold text-[#F97316]">₹{{ number_format($order->total_amount, 2) }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No orders yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const ctx = document.getElementById('revenueChart');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($chartData) !!},
                    backgroundColor: '#F97316',
                }]
            },
            options: {
                scales: {
                    x: { ticks: { color: '#9CA3AF' }, grid: { color: '#1F2937' } },
                    y: { ticks: { color: '#9CA3AF' }, grid: { color: '#1F2937' } }
                },
                plugins: { legend: { labels: { color: '#9CA3AF' } } }
            }
        });
    </script>
</body>
</html>