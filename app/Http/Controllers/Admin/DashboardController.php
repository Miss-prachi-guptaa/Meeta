<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Vendor;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('payment_status', 'paid')
            ->sum('total_amount');

        $activeVendors = Vendor::where('status', 'approved')
            ->count();

        $pendingVendors = Vendor::where('status', 'pending')
            ->count();

        $totalOrders = Order::count();

        $todayOrders = Order::whereDate('created_at', today())
            ->count();

        $pendingProducts = Product::where('status', 'pending')
            ->count();

        // Last 7 days
        $days = collect(range(6, 0))
            ->map(fn ($i) => now()->subDays($i));

        $chartLabels = $days->map(
            fn ($day) => $day->format('D')
        );

        // Revenue chart
        $chartData = $days->map(function ($day) {
            return Order::where('payment_status', 'paid')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total_amount');
        });

        // Orders chart
        $orderChartData = $days->map(function ($day) {
            return Order::whereDate(
                'created_at',
                $day->toDateString()
            )->count();
        });

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'total_revenue' => $totalRevenue,
                    'active_vendors' => $activeVendors,
                    'pending_vendors' => $pendingVendors,
                    'total_orders' => $totalOrders,
                    'today_orders' => $todayOrders,
                    'pending_products' => $pendingProducts,
                ],

                'revenue_chart' => [
                    'labels' => $chartLabels,
                    'data' => $chartData,
                ],

                'orders_chart' => [
                    'labels' => $chartLabels,
                    'data' => $orderChartData,
                ],

                'recent_orders' => $recentOrders,
            ],
        ]);
    }
}