<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SubOrder;
use App\Models\Inventory;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Display a paginated listing of Master Orders & Sub-Orders with SLA status and financial metrics.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'status' => ['nullable', 'string', 'max:50'],
                'vendor_id' => ['nullable', 'integer', 'exists:vendors,id'],
                'search' => ['nullable', 'string', 'max:100'],
                'sla_breached' => ['nullable', 'boolean'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            ]);

            // SLA cutoff: Vendor must confirm within 24 hours of order placement
            $slaCutoff = Carbon::now()->subHours(24);

            $query = Order::with([
                'user:id,name,email,phone',
                'subOrders.vendor:id,store_name,contact_number,email',
                'subOrders.orderItems:id,sub_order_id,product_name,variant_name,sku,quantity,unit_price,total_amount',
                'subOrders.shipments.tracking',
                'payments',
            ]);

            // Filter by search (Order number, customer name, customer phone)
            if (!empty($data['search'])) {
                $search = $data['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                        ->orWhere('shipping_full_name', 'like', "%{$search}%")
                        ->orWhere('shipping_phone', 'like', "%{$search}%")
                        ->orWhereHas('subOrders', function ($sub) use ($search) {
                            $sub->where('sub_order_number', 'like', "%{$search}%")
                                ->orWhereHas('vendor', fn ($v) => $v->where('store_name', 'like', "%{$search}%"));
                        });
                });
            }

            // Filter by sub-order status or master order status
            if (!empty($data['status'])) {
                $status = $data['status'];
                $query->where(function ($q) use ($status) {
                    $q->where('order_status', $status)
                        ->orWhereHas('subOrders', fn ($sub) => $sub->where('order_status', $status));
                });
            }

            // Filter by vendor
            if (!empty($data['vendor_id'])) {
                $query->whereHas('subOrders', fn ($sub) => $sub->where('vendor_id', $data['vendor_id']));
            }

            // Filter by SLA breach: newly placed/confirmed sub-orders that haven't been accepted within 24h
            if (!empty($data['sla_breached'])) {
                $query->whereHas('subOrders', function ($sub) use ($slaCutoff) {
                    $sub->where('order_status', 'confirmed')
                        ->whereNull('confirmed_at')
                        ->where('created_at', '<', $slaCutoff);
                });
            }

            $orders = $query->latest()->paginate($data['per_page'] ?? 15);

            // Compute SLA remaining time or breach for each sub-order
            $orders->getCollection()->transform(function ($order) {
                $order->subOrders->transform(function ($sub) {
                    $createdAt = Carbon::parse($sub->created_at);
                    $deadline = $createdAt->copy()->addHours(24);
                    $now = Carbon::now();

                    $isBreached = false;
                    $remainingMinutes = 0;

                    if (in_array($sub->order_status, ['placed', 'confirmed']) && is_null($sub->confirmed_at)) {
                        if ($now->greaterThan($deadline)) {
                            $isBreached = true;
                            $remainingMinutes = 0;
                        } else {
                            $remainingMinutes = $now->diffInMinutes($deadline, false);
                        }
                    }

                    $sub->sla = [
                        'deadline' => $deadline->toIso8601String(),
                        'is_breached' => $isBreached,
                        'remaining_minutes' => max(0, $remainingMinutes),
                        'remaining_human' => $isBreached ? 'Expired (SLA Breached)' : ($remainingMinutes > 0 ? floor($remainingMinutes / 60) . 'h ' . ($remainingMinutes % 60) . 'm' : 'Confirmed'),
                    ];

                    return $sub;
                });
                return $order;
            });

            // Summary stats for KPI cards
            $stats = [
                'total_orders' => Order::count(),
                'total_sub_orders' => SubOrder::count(),
                'pending_confirmation' => SubOrder::where('order_status', 'confirmed')->whereNull('confirmed_at')->count(),
                'sla_breached_count' => SubOrder::where('order_status', 'confirmed')->whereNull('confirmed_at')->where('created_at', '<', $slaCutoff)->count(),
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Admin orders and sub-orders retrieved successfully.',
                'data' => [
                    'stats' => $stats,
                    'orders' => $orders,
                ],
            ]);
        });
    }

    /**
     * Show single Master Order details with all vendor sub-orders, items, tracking & payments.
     */
    public function show(int $id)
    {
        return $this->execute(function () use ($id) {
            $order = Order::with([
                'user:id,name,email,phone',
                'subOrders.vendor:id,store_name,contact_number,email',
                'subOrders.orderItems.product.images',
                'subOrders.orderItems.productVariant',
                'subOrders.shipments.tracking' => fn ($q) => $q->orderBy('tracked_at', 'desc'),
                'subOrders.settlement',
                'payments',
                'returns.items',
            ])->findOrFail($id);

            // Compute SLA for each sub-order
            $order->subOrders->transform(function ($sub) {
                $createdAt = Carbon::parse($sub->created_at);
                $deadline = $createdAt->copy()->addHours(24);
                $now = Carbon::now();

                $isBreached = false;
                $remainingMinutes = 0;

                if (in_array($sub->order_status, ['placed', 'confirmed']) && is_null($sub->confirmed_at)) {
                    if ($now->greaterThan($deadline)) {
                        $isBreached = true;
                    } else {
                        $remainingMinutes = $now->diffInMinutes($deadline, false);
                    }
                }

                $sub->sla = [
                    'deadline' => $deadline->toIso8601String(),
                    'is_breached' => $isBreached,
                    'remaining_minutes' => max(0, $remainingMinutes),
                    'remaining_human' => $isBreached ? 'Expired' : ($remainingMinutes > 0 ? floor($remainingMinutes / 60) . 'h ' . ($remainingMinutes % 60) . 'm' : 'N/A'),
                ];

                return $sub;
            });

            return response()->json([
                'success' => true,
                'message' => 'Order details retrieved successfully.',
                'data' => $order,
            ]);
        });
    }

    /**
     * Admin action to manually trigger Auto-Cancel on an SLA-breached Sub-Order and restock inventory.
     */
    public function autoCancelSubOrder(Request $request, int $subOrderId)
    {
        return $this->execute(function () use ($request, $subOrderId) {
            $data = $request->validate([
                'reason' => ['nullable', 'string', 'max:500'],
            ]);

            return DB::transaction(function () use ($subOrderId, $data) {
                $subOrder = SubOrder::with('orderItems')->lockForUpdate()->findOrFail($subOrderId);

                if (in_array($subOrder->order_status, ['cancelled', 'delivered', 'shipped'])) {
                    throw ValidationException::withMessages([
                        'sub_order' => ["Sub-order cannot be cancelled because it is already {$subOrder->order_status}."],
                    ]);
                }

                // Restock inventory
                foreach ($subOrder->orderItems as $item) {
                    if ($item->product_variant_id) {
                        Inventory::where('product_variant_id', $item->product_variant_id)->increment('quantity', $item->quantity);
                    }
                }

                $reason = $data['reason'] ?? 'Vendor confirmation SLA timeout (24 hours exceeded). Auto-cancelled by Admin.';
                $subOrder->update([
                    'order_status' => 'cancelled',
                    'cancelled_at' => Carbon::now(),
                    'cancellation_reason' => $reason,
                ]);

                // Check if all sub-orders of the master order are cancelled
                $parentOrder = $subOrder->order;
                $activeSubOrders = $parentOrder->subOrders()->where('order_status', '!=', 'cancelled')->count();
                if ($activeSubOrders === 0) {
                    $parentOrder->update([
                        'order_status' => 'cancelled',
                        'cancellation_reason' => 'All vendor sub-orders cancelled.',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Sub-order has been cancelled and inventory restocked successfully.',
                    'data' => $subOrder,
                ]);
            });
        });
    }
}
