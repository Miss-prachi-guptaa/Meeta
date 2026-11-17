<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\SubOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReturnController extends Controller
{
    /**
     * Customer submits return request for items within 7 days of delivery.
     */
    public function store(Request $request, int $subOrderId)
    {
        return $this->execute(function () use ($request, $subOrderId) {
            $user = $request->user();

            $data = $request->validate([
                'reason' => ['required', 'string', 'min:5', 'max:500'],
                'customer_note' => ['nullable', 'string', 'max:1000'],
                'items' => ['required', 'array', 'min:1'],
                'items.*.order_item_id' => ['required', 'integer', 'exists:order_items,id'],
                'items.*.quantity' => ['required', 'integer', 'min:1'],
            ]);

            return DB::transaction(function () use ($user, $subOrderId, $data) {
                $subOrder = SubOrder::with('order')->whereHas('order', fn ($q) => $q->where('user_id', $user->id))
                    ->findOrFail($subOrderId);

                // 1. Must be delivered
                if ($subOrder->order_status !== 'delivered') {
                    throw ValidationException::withMessages([
                        'order' => ['Returns can only be requested on delivered orders.'],
                    ]);
                }

                // 2. Must be within 7 days of delivery
                $deliveredAt = $subOrder->delivered_at ?? $subOrder->updated_at;
                if (Carbon::now()->diffInDays($deliveredAt) > 7) {
                    throw ValidationException::withMessages([
                        'return_window' => ['The 7-day return policy window for this order has expired.'],
                    ]);
                }

                $returnCase = ReturnModel::create([
                    'return_number' => 'RET-' . strtoupper(uniqid()),
                    'order_id' => $subOrder->order_id,
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'reason' => $data['reason'],
                    'customer_note' => $data['customer_note'] ?? null,
                    'requested_at' => Carbon::now(),
                ]);

                foreach ($data['items'] as $item) {
                    ReturnItem::create([
                        'return_id' => $returnCase->id,
                        'order_item_id' => $item['order_item_id'],
                        'quantity' => $item['quantity'],
                        'status' => 'pending',
                        'item_condition' => 'opened',
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Return request submitted successfully. The vendor and admin will review it.',
                    'data' => $returnCase->load('items.orderItem'),
                ]);
            });
        });
    }

    /**
     * Customer returns list.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $user = $request->user();

            $returns = ReturnModel::where('user_id', $user->id)
                ->with(['items.orderItem.product:id,name', 'order:id,order_number'])
                ->latest()
                ->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Customer returns fetched successfully.',
                'data' => $returns,
            ]);
        });
    }
}
