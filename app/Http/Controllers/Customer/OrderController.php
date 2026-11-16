<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Customer\OrderDetailResource;
use App\Http\Resources\Customer\OrderListResource;
use App\Models\Inventory;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $filters = $request->validate([
                'status' => ['nullable', 'in:pending,confirmed,processing,shipped,delivered,cancelled,returned'],
                'year' => ['nullable', 'integer', 'min:2020', 'max:'.now()->year],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);
            $orders = $request->user()->orders()->with(['subOrders.orderItems.product.images'])
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('order_status', $status))
                ->when($filters['year'] ?? null, fn ($query, $year) => $query->whereYear('created_at', $year))
                ->latest()->paginate($filters['per_page'] ?? 10)->withQueryString();

            return response()->json(['success' => true, 'message' => 'Orders fetched successfully.', 'data' => [
                'orders' => OrderListResource::collection($orders->getCollection()),
                'pagination' => [
                    'current_page' => $orders->currentPage(), 'last_page' => $orders->lastPage(), 'per_page' => $orders->perPage(),
                    'total' => $orders->total(), 'from' => $orders->firstItem(), 'to' => $orders->lastItem(),
                ],
            ]]);
        });
    }

    public function show(Request $request, int $order)
    {
        return $this->execute(function () use ($request, $order) {
            $model = $request->user()->orders()->whereKey($order)->with(['subOrders.vendor:id,store_name,store_slug', 'subOrders.orderItems.product.images', 'subOrders.shipments.tracking', 'payments'])->firstOrFail();

            return response()->json(['success' => true, 'message' => 'Order fetched successfully.', 'data' => new OrderDetailResource($model)]);
        });
    }

    public function tracking(Request $request, int $order)
    {
        return $this->execute(function () use ($request, $order) {
            $model = $request->user()->orders()->whereKey($order)->with([
                'subOrders.shipments' => fn ($query) => $query->with(['tracking' => fn ($tracking) => $tracking->orderBy('tracked_at')]),
            ])->firstOrFail();

            return response()->json(['success' => true, 'message' => 'Shipment tracking fetched successfully.', 'data' => [
                'order_id' => $model->id, 'order_number' => $model->order_number, 'order_status' => $model->order_status,
                'shipments' => $model->subOrders->flatMap->shipments->values(),
            ]]);
        });
    }

    public function invoice(Request $request, int $order)
    {
        return $this->execute(function () use ($request, $order) {
            $model = $request->user()->orders()->whereKey($order)->with(['subOrders.vendor:id,store_name', 'subOrders.orderItems.product', 'payments'])->firstOrFail();

            return response()->json(['success' => true, 'message' => 'Invoice data fetched successfully.', 'data' => [
                'invoice_number' => 'INV-'.$model->order_number,
                'issued_at' => $model->created_at,
                'order' => $model,
                'billing_note' => 'GST and applicable taxes are included in the displayed amounts.',
                'download_format' => null,
            ]]);
        });
    }

    public function cancel(Request $request, int $order)
    {
        return $this->execute(function () use ($request, $order) {
            $data = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
            $model = $request->user()->orders()->whereKey($order)->with(['subOrders.orderItems'])->firstOrFail();
            if (! in_array($model->order_status, ['pending', 'confirmed'], true)) {
                throw ValidationException::withMessages(['order' => ['This order can no longer be cancelled.']]);
            }
            if ($model->payment_status === 'paid') {
                throw ValidationException::withMessages(['order' => ['Paid orders require a refund request.']]);
            }

            DB::transaction(function () use ($request, $model, $data) {
                $lockedOrder = $request->user()->orders()->whereKey($model->id)->lockForUpdate()->firstOrFail();
                if (! in_array($lockedOrder->order_status, ['pending', 'confirmed'], true)) {
                    throw ValidationException::withMessages(['order' => ['This order can no longer be cancelled.']]);
                }
                if ($lockedOrder->payment_status === 'paid') {
                    throw ValidationException::withMessages(['order' => ['Paid orders require a refund request.']]);
                }
                $lockedOrder->load(['subOrders.orderItems']);
                foreach ($lockedOrder->subOrders as $subOrder) {
                    foreach ($subOrder->orderItems as $item) {
                        Inventory::where('product_variant_id', $item->product_variant_id)->increment('quantity', $item->quantity);
                    }
                    $subOrder->update(['order_status' => 'cancelled', 'cancelled_at' => now(), 'cancellation_reason' => $data['reason']]);
                }
                $lockedOrder->update(['order_status' => 'cancelled', 'cancellation_reason' => $data['reason']]);
                $lockedOrder->payments()->where('status', 'pending')->update(['status' => 'cancelled']);
                Notification::create(['user_id' => $request->user()->id, 'title' => 'Order cancelled', 'message' => "Order {$model->order_number} has been cancelled.",
                    'type' => 'order_cancelled', 'channel' => 'database', 'reference_type' => 'order', 'reference_id' => $model->id]);
            });

            return response()->json(['success' => true, 'message' => 'Order cancelled successfully.', 'data' => ['id' => $model->id, 'order_number' => $model->order_number, 'status' => 'cancelled']]);
        });
    }
}
