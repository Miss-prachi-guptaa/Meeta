<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Shipment;
use App\Models\SubOrder;
use App\Services\Vendor\OrderLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(private readonly OrderLifecycleService $lifecycle) {}

    /**
     * Use case: vendor order screen with filters, search, pagination and status counters.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'status' => ['nullable', Rule::in(['confirmed', 'processing', 'ready_to_ship', 'shipped', 'delivered', 'cancelled'])],
                'search' => ['nullable', 'string', 'max:100'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);
            $vendor = $this->vendor($request);
            $base = $vendor->subOrders();
            $counts = (clone $base)->selectRaw('order_status, COUNT(*) as total')->groupBy('order_status')->pluck('total', 'order_status');
            $orders = $base->with(['order:id,order_number,user_id,shipping_full_name,shipping_city,shipping_state,total_amount,payment_status,order_status,created_at', 'orderItems:id,sub_order_id,product_name,variant_name,quantity,total_amount'])
                ->when($data['status'] ?? null, fn ($query, $status) => $query->where('order_status', $status))
                ->when($data['search'] ?? null, fn ($query, $search) => $query->where(fn ($nested) => $nested
                    ->where('sub_order_number', 'like', "%{$search}%")
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$search}%"))))
                ->latest()->paginate($data['per_page'] ?? 15)->withQueryString();

            return response()->json(['success' => true, 'message' => 'Vendor orders fetched successfully.', 'data' => ['counts' => $counts, 'orders' => $orders]]);
        });
    }

    /**
     * Use case: vendor opens one assigned order to pack it and view customer delivery details.
     */
    public function show(Request $request, int $subOrder)
    {
        return $this->execute(function () use ($request, $subOrder) {
            $order = $this->ownedOrder($request, $subOrder)->load([
                'order:id,order_number,user_id,shipping_full_name,shipping_phone,shipping_address_line_1,shipping_address_line_2,shipping_landmark,shipping_city,shipping_state,shipping_country,shipping_postal_code,subtotal,shipping_amount,total_amount,payment_status,order_status,notes,created_at',
                'orderItems.product.images', 'shipments.tracking' => fn ($query) => $query->orderBy('tracked_at'),
            ]);

            return response()->json(['success' => true, 'message' => 'Vendor order fetched successfully.', 'data' => $order]);
        });
    }

    /**
     * Use case: vendor accepts an order for packing, or rejects it before processing with a reason.
     */
    public function updateStatus(Request $request, int $subOrder)
    {
        return $this->execute(function () use ($request, $subOrder) {
            $data = $request->validate([
                'status' => ['required', Rule::in(['processing', 'cancelled'])],
                'reason' => ['required_if:status,cancelled', 'nullable', 'string', 'min:5', 'max:500'],
            ]);

            $updated = DB::transaction(function () use ($request, $subOrder, $data) {
                $order = $this->ownedOrder($request, $subOrder, true)->load(['orderItems', 'order']);
                if ($order->order_status !== 'confirmed') {
                    throw ValidationException::withMessages(['status' => ['Only a newly confirmed order can be accepted or rejected.']]);
                }

                if ($data['status'] === 'cancelled') {
                    foreach ($order->orderItems as $item) {
                        Inventory::where('product_variant_id', $item->product_variant_id)->increment('quantity', $item->quantity);
                    }
                    $order->update(['order_status' => 'cancelled', 'cancelled_at' => now(), 'cancellation_reason' => $data['reason']]);
                    $this->lifecycle->notifyCustomer($order, 'Item order cancelled', "{$order->sub_order_number} was cancelled by the vendor. Reason: {$data['reason']}", 'order_cancelled');
                } else {
                    $order->update([
                        'order_status' => 'processing',
                        'confirmed_at' => now(),
                    ]);
                    $this->lifecycle->notifyCustomer($order, 'Order is being prepared', "{$order->sub_order_number} has been accepted and is being prepared.", 'order_processing');
                }

                $this->lifecycle->syncParentOrder($order->order);

                return $order->fresh(['order', 'orderItems']);
            });

            return response()->json(['success' => true, 'message' => $data['status'] === 'processing' ? 'Order accepted successfully.' : 'Order rejected successfully.', 'data' => $updated]);
        });
    }

    /**
     * Use case: after packing, vendor records package details and requests third-party pickup.
     */
    public function createShipment(Request $request, int $subOrder)
    {
        return $this->execute(function () use ($request, $subOrder) {
            $data = $request->validate([
                'weight' => ['required', 'numeric', 'gt:0', 'max:999.999'],
                'length' => ['required', 'numeric', 'gt:0', 'max:999.99'],
                'width' => ['required', 'numeric', 'gt:0', 'max:999.99'],
                'height' => ['required', 'numeric', 'gt:0', 'max:999.99'],
                'shipping_partner' => ['nullable', 'string', 'max:100'],
                'expected_delivery_at' => ['nullable', 'date', 'after:now'],
                'remarks' => ['nullable', 'string', 'max:1000'],
            ]);

            $shipment = DB::transaction(function () use ($request, $subOrder, $data) {
                $order = $this->ownedOrder($request, $subOrder, true)->load('order');
                if ($order->order_status !== 'processing') {
                    throw ValidationException::withMessages(['order' => ['Only a processing order can be marked ready for pickup.']]);
                }
                if ($order->shipments()->exists()) {
                    throw ValidationException::withMessages(['order' => ['A shipment already exists for this order.']]);
                }

                $shipment = $order->shipments()->create([
                    'shipment_number' => 'SHP-'.strtoupper(Str::random(14)),
                    'provider' => 'dummy',
                    'shipping_partner' => $data['shipping_partner'] ?? 'Dummy Logistics',
                    'tracking_number' => 'DUM'.now()->format('ymd').strtoupper(Str::random(8)),
                    'status' => 'ready_for_pickup',
                    'package_weight' => $data['weight'],
                    'package_length' => $data['length'],
                    'package_width' => $data['width'],
                    'package_height' => $data['height'],
                    'expected_delivery_at' => $data['expected_delivery_at'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                ]);
                $shipment->tracking()->create(['status' => 'ready_for_pickup', 'description' => 'Package is ready for third-party courier pickup.', 'tracked_at' => now()]);
                $order->update(['order_status' => 'ready_to_ship']);
                $this->lifecycle->syncParentOrder($order->order);
                $this->lifecycle->notifyCustomer($order, 'Order packed', "{$order->sub_order_number} is packed and ready for pickup.", 'order_ready_to_ship');

                return $shipment->fresh('tracking');
            });

            return response()->json(['success' => true, 'message' => 'Shipment created and pickup requested successfully.', 'data' => $shipment], 201);
        });
    }

    /**
     * Use case: dummy courier/admin simulation updates tracking until a real provider webhook is integrated.
     */
    public function updateShipmentStatus(Request $request, int $shipment)
    {
        return $this->execute(function () use ($request, $shipment) {
            $data = $request->validate([
                'status' => ['required', Rule::in(['picked_up', 'in_transit', 'out_for_delivery', 'delivered'])],
                'location' => ['nullable', 'string', 'max:150'],
                'description' => ['nullable', 'string', 'max:500'],
            ]);

            $updated = DB::transaction(function () use ($request, $shipment, $data) {
                $model = Shipment::whereKey($shipment)->whereHas('subOrder', fn ($query) => $query->where('vendor_id', $this->vendor($request)->id))->lockForUpdate()->firstOrFail();
                if ($model->provider !== 'dummy') {
                    throw ValidationException::withMessages(['shipment' => ['Real provider shipments can only be updated by their courier webhook.']]);
                }
                $allowed = ['ready_for_pickup' => 'picked_up', 'picked_up' => 'in_transit', 'in_transit' => 'out_for_delivery', 'out_for_delivery' => 'delivered'];
                if (($allowed[$model->status] ?? null) !== $data['status']) {
                    throw ValidationException::withMessages(['status' => ["Shipment cannot move from {$model->status} to {$data['status']}."]]);
                }

                $model->update([
                    'status' => $data['status'],
                    'shipped_at' => $data['status'] === 'picked_up' ? now() : $model->shipped_at,
                    'delivered_at' => $data['status'] === 'delivered' ? now() : null,
                ]);
                $model->tracking()->create(['status' => $data['status'], 'location' => $data['location'] ?? null, 'description' => $data['description'] ?? null, 'tracked_at' => now()]);

                $order = $model->subOrder()->with('order')->firstOrFail();
                if ($data['status'] === 'picked_up') {
                    $order->update(['order_status' => 'shipped', 'shipped_at' => now()]);
                } elseif ($data['status'] === 'delivered') {
                    $order->update(['order_status' => 'delivered', 'delivered_at' => now()]);
                }
                $this->lifecycle->syncParentOrder($order->order);
                $this->lifecycle->notifyCustomer($order, 'Shipment update', "{$order->sub_order_number} is now ".str_replace('_', ' ', $data['status']).'.', 'shipment_update');

                return $model->fresh('tracking');
            });

            return response()->json(['success' => true, 'message' => 'Shipment status updated successfully.', 'data' => $updated]);
        });
    }

    private function vendor(Request $request)
    {
        $vendor = $request->user()->vendor;
        if (! $vendor) {
            throw ValidationException::withMessages(['vendor' => ['Vendor profile is required.']]);
        }

        return $vendor;
    }

    private function ownedOrder(Request $request, int $id, bool $lock = false): SubOrder
    {
        $query = $this->vendor($request)->subOrders()->whereKey($id);

        return ($lock ? $query->lockForUpdate() : $query)->firstOrFail();
    }
}
