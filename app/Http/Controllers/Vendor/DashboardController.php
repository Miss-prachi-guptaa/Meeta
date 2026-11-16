<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\OrderItem;
use App\Models\SubOrder;
use App\Models\Vendor;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{
    /**
     * Use case: provide one frontend-ready overview of a vendor's catalogue,
     * fulfilment workload, sales, stock health and settlement position.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'range' => ['nullable', Rule::in(['week', 'month'])],
        ]);

        $vendor = $this->vendor($request);
        $range = $validated['range'] ?? 'week';
        [$from, $to] = $this->dateWindow($range);
        $previousTo = $from->copy()->subSecond();
        $previousFrom = $previousTo->copy()->subSeconds($from->diffInSeconds($to));

        $productCounts = $vendor->products()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');
        $orderCounts = $vendor->subOrders()
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->pluck('total', 'order_status');

        $salesOrders = $vendor->subOrders()
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->get(['id', 'total_amount', 'created_at']);
        $previousSales = (float) $vendor->subOrders()
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$previousFrom, $previousTo])
            ->sum('total_amount');
        $currentSales = (float) $salesOrders->sum('total_amount');

        $recentOrders = $vendor->subOrders()
            ->with(['order:id,order_number,shipping_full_name,payment_status,created_at'])
            ->withCount('orderItems')
            ->latest()
            ->limit(6)
            ->get()
            ->map(fn (SubOrder $subOrder) => [
                'id' => $subOrder->id,
                'sub_order_number' => $subOrder->sub_order_number,
                'order_number' => $subOrder->order?->order_number,
                'customer_name' => $subOrder->order?->shipping_full_name,
                'amount' => (float) $subOrder->total_amount,
                'status' => $subOrder->order_status,
                'payment_status' => $subOrder->order?->payment_status,
                'items_count' => $subOrder->order_items_count,
                'created_at' => $subOrder->created_at,
            ]);

        $lowStockQuery = Inventory::query()
            ->whereHas('productVariant.product', fn ($query) => $query->where('vendor_id', $vendor->id))
            ->whereRaw('(quantity - reserved_quantity) <= COALESCE(minimum_stock, 0)');
        $lowStockCount = (clone $lowStockQuery)->count();
        $lowStock = $lowStockQuery
            ->with('productVariant:id,product_id,name,sku')
            ->orderByRaw('(quantity - reserved_quantity) asc')
            ->limit(6)
            ->get()
            ->map(fn (Inventory $inventory) => [
                'variant_id' => $inventory->product_variant_id,
                'product_id' => $inventory->productVariant?->product_id,
                'variant_name' => $inventory->productVariant?->name,
                'sku' => $inventory->productVariant?->sku,
                'quantity' => $inventory->quantity,
                'reserved_quantity' => $inventory->reserved_quantity,
                'available_quantity' => max(0, $inventory->quantity - $inventory->reserved_quantity),
                'minimum_stock' => $inventory->minimum_stock,
            ]);

        $settlements = $vendor->settlements();
        $paidSales = (float) $vendor->subOrders()
            ->where('order_status', '!=', 'cancelled')
            ->whereBetween('created_at', [$from, $to])
            ->whereHas('order', fn ($query) => $query->where('payment_status', 'paid'))
            ->sum('total_amount');

        return response()->json([
            'success' => true,
            'message' => 'Vendor dashboard fetched successfully.',
            'data' => [
                'generated_at' => now(),
                'range' => ['key' => $range, 'from' => $from->toDateString(), 'to' => $to->toDateString()],
                'vendor' => [
                    'id' => $vendor->id,
                    'store_name' => $vendor->store_name,
                    'store_slug' => $vendor->store_slug,
                    'status' => $vendor->status,
                ],
                'summary' => [
                    'total_products' => (int) $productCounts->sum(),
                    'active_products' => (int) ($productCounts['active'] ?? 0),
                    'pending_products' => (int) ($productCounts['pending'] ?? 0),
                    'total_orders' => (int) $orderCounts->sum(),
                    'action_required_orders' => (int) (($orderCounts['confirmed'] ?? 0) + ($orderCounts['processing'] ?? 0)),
                    'period_sales' => round($currentSales, 2),
                    'paid_sales' => round($paidSales, 2),
                    'average_order_value' => $salesOrders->isEmpty() ? 0 : round($currentSales / $salesOrders->count(), 2),
                    'low_stock_variants' => $lowStockCount,
                ],
                // Legacy top-level fields keep the current dashboard and external integrations compatible.
                'total_products' => (int) $productCounts->sum(),
                'active_products' => (int) ($productCounts['active'] ?? 0),
                'pending_products' => (int) ($productCounts['pending'] ?? 0),
                'total_orders' => (int) $orderCounts->sum(),
                'product_status_counts' => $this->statusCounts($productCounts, ['draft', 'pending', 'active', 'rejected', 'inactive']),
                'order_status_counts' => $this->statusCounts($orderCounts, ['confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'cancelled']),
                'sales' => $this->salesTrend($salesOrders, $from, $to, $range, $currentSales, $previousSales),
                'settlements' => [
                    'pending_amount' => round((float) (clone $settlements)->whereIn('status', ['pending', 'available'])->sum('net_amount'), 2),
                    'settled_amount' => round((float) (clone $settlements)->where('status', 'settled')->sum('net_amount'), 2),
                    'next_available_at' => (clone $settlements)->whereIn('status', ['pending', 'available'])->whereNotNull('available_at')->min('available_at'),
                ],
                'recent_orders' => $recentOrders,
                'low_stock' => $lowStock,
                'top_products' => $this->topProducts($vendor, $from, $to),
                'attention' => [
                    'new_orders' => (int) ($orderCounts['confirmed'] ?? 0),
                    'orders_to_pack' => (int) ($orderCounts['processing'] ?? 0),
                    'ready_for_pickup' => (int) ($orderCounts['ready_to_ship'] ?? 0),
                    'rejected_products' => (int) ($productCounts['rejected'] ?? 0),
                    'low_stock_variants' => $lowStockCount,
                ],
            ],
        ]);
    }

    private function vendor(Request $request): Vendor
    {
        $vendor = $request->user()->vendor;
        if (! $vendor) {
            throw ValidationException::withMessages(['vendor' => ['Vendor profile is required.']]);
        }
        if (! $vendor->is_approved) {
            throw ValidationException::withMessages(['vendor' => ['Only approved vendors can access the dashboard.']]);
        }

        return $vendor;
    }

    private function dateWindow(string $range): array
    {
        $to = now()->endOfDay();
        $from = $range === 'month' ? now()->startOfMonth() : now()->startOfWeek();

        return [$from, $to];
    }

    private function statusCounts(Collection $counts, array $statuses): array
    {
        return collect($statuses)->mapWithKeys(fn ($status) => [$status => (int) ($counts[$status] ?? 0)])->all();
    }

    private function salesTrend(Collection $orders, Carbon $from, Carbon $to, string $range, float $current, float $previous): array
    {
        $daily = $orders->groupBy(fn (SubOrder $order) => $order->created_at->toDateString())
            ->map(fn (Collection $items) => round((float) $items->sum('total_amount'), 2));
        $dates = collect(CarbonPeriod::create($from->copy()->startOfDay(), $to->copy()->startOfDay()));

        return [
            'range' => $range,
            'labels' => $dates->map(fn (Carbon $date) => $range === 'week' ? $date->format('D') : $date->format('j M'))->values(),
            'values' => $dates->map(fn (Carbon $date) => (float) ($daily[$date->toDateString()] ?? 0))->values(),
            'total' => round($current, 2),
            'previous_total' => round($previous, 2),
            'change_percentage' => $previous > 0 ? round((($current - $previous) / $previous) * 100, 1) : null,
        ];
    }

    private function topProducts(Vendor $vendor, Carbon $from, Carbon $to): Collection
    {
        return OrderItem::query()
            ->join('sub_orders', 'sub_orders.id', '=', 'order_items.sub_order_id')
            ->where('sub_orders.vendor_id', $vendor->id)
            ->whereNull('sub_orders.deleted_at')
            ->where('sub_orders.order_status', '!=', 'cancelled')
            ->whereBetween('sub_orders.created_at', [$from, $to])
            ->groupBy('order_items.product_id', 'order_items.product_name')
            ->orderByDesc(DB::raw('SUM(order_items.quantity)'))
            ->limit(5)
            ->get([
                'order_items.product_id',
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as units_sold'),
                DB::raw('SUM(order_items.total_amount) as revenue'),
            ])
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'units_sold' => (int) $item->units_sold,
                'revenue' => round((float) $item->revenue, 2),
            ]);
    }
}
