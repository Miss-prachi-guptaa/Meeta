<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
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
     * List all return requests for products belonging to the logged-in vendor.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $vendor = $this->vendor($request);

            $data = $request->validate([
                'status' => ['nullable', 'string', 'max:50'],
                'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
            ]);

            // Query return cases that have items belonging to this vendor's sub-orders
            $returns = ReturnModel::whereHas('items.orderItem.subOrder', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->with([
                'user:id,name,email,phone',
                'order:id,order_number,created_at',
                'items' => function ($q) use ($vendor) {
                    $q->whereHas('orderItem.subOrder', fn ($sub) => $sub->where('vendor_id', $vendor->id))
                        ->with(['orderItem.product:id,name', 'orderItem.productVariant']);
                },
            ])
            ->when(!empty($data['status']), fn ($q) => $q->where('status', $data['status']))
            ->latest()
            ->paginate($data['per_page'] ?? 15);

            return response()->json([
                'success' => true,
                'message' => 'Vendor return requests retrieved successfully.',
                'data' => $returns,
            ]);
        });
    }

    /**
     * Show single return request detail for this vendor.
     */
    public function show(Request $request, int $id)
    {
        return $this->execute(function () use ($request, $id) {
            $vendor = $this->vendor($request);

            $returnCase = ReturnModel::whereHas('items.orderItem.subOrder', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })
            ->with([
                'user:id,name,email,phone',
                'order:id,order_number,created_at,shipping_full_name,shipping_city',
                'items' => function ($q) use ($vendor) {
                    $q->whereHas('orderItem.subOrder', fn ($sub) => $sub->where('vendor_id', $vendor->id))
                        ->with(['orderItem.product.images', 'orderItem.productVariant']);
                },
            ])
            ->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Return request details retrieved successfully.',
                'data' => $returnCase,
            ]);
        });
    }

    /**
     * Vendor Action on Return Request:
     * - 'accept': Vendor accepts return -> Marks return approved for customer pickup.
     * - 'dispute': Vendor disputes claim (e.g. tamper seal missing or fake damage) -> Escalates to Admin Dispute queue.
     */
    public function respond(Request $request, int $id)
    {
        return $this->execute(function () use ($request, $id) {
            $vendor = $this->vendor($request);

            $data = $request->validate([
                'action' => ['required', Rule::in(['accept', 'dispute'])],
                'notes' => ['required', 'string', 'min:5', 'max:1000'],
            ]);

            return DB::transaction(function () use ($id, $vendor, $data) {
                $returnCase = ReturnModel::whereHas('items.orderItem.subOrder', function ($q) use ($vendor) {
                    $q->where('vendor_id', $vendor->id);
                })->lockForUpdate()->findOrFail($id);

                if (!in_array($returnCase->status, ['pending', 'requested'])) {
                    throw ValidationException::withMessages([
                        'return' => ["Return request cannot be updated because it is already {$returnCase->status}."],
                    ]);
                }

                if ($data['action'] === 'accept') {
                    $returnCase->update([
                        'status' => 'approved',
                        'admin_note' => 'Vendor accepted return request: ' . $data['notes'],
                        'approved_at' => Carbon::now(),
                    ]);
                    $message = 'Return request accepted successfully. Pickup will be arranged.';
                } else {
                    // Escalated to Admin Dispute Panel
                    $returnCase->update([
                        'status' => 'disputed',
                        'admin_note' => 'Vendor disputed return claim: ' . $data['notes'] . ' (Awaiting Admin Resolution)',
                    ]);
                    $message = 'Return request has been disputed and escalated to Admin for resolution.';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => $returnCase,
                ]);
            });
        });
    }

    private function vendor(Request $request)
    {
        $user = $request->user();
        $vendor = $user->vendor;
        if (!$vendor || $vendor->status !== 'approved') {
            abort(403, 'Unauthorized. Approved vendor account required.');
        }
        return $vendor;
    }
}
