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
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');

        $activeVendors = Vendor::where('status', 'approved')->count();
        $pendingVendors = Vendor::where('status', 'pending')->count();

        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();

        $pendingProducts = Product::where('status', 'pending')->count();

        // Last 7 days revenue for the chart
        $days = collect(range(6, 0))->map(fn ($i) => now()->subDays($i));
        $chartLabels = $days->map(fn ($d) => $d->format('D'));
        $chartData = $days->map(function ($day) {
            return Order::where('payment_status', 'paid')
                ->whereDate('created_at', $day->toDateString())
                ->sum('total_amount');
        });

        $recentOrders = Order::with('user')->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'activeVendors',
            'pendingVendors',
            'totalOrders',
            'todayOrders',
            'pendingProducts',
            'chartLabels',
            'chartData',
            'recentOrders'
        ));
    }
}