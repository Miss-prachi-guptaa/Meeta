<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\ReturnModel;
use App\Models\VendorLedger;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DisputeController extends Controller
{
    /**
     * List all return and dispute cases.
     */
    public function index(Request $request)
    {
        return $this->execute(function () use ($request) {
            $data = $request->validate([
                'status' => ['nullable', 'string'],
                'search' => ['nullable', 'string'],
                'per_page' => ['nullable', 'integer'],
            ]);

            $query = ReturnModel::with([
                'user:id,name,email,phone',
                'order:id,order_number,total_amount,payment_status',
                'items.orderItem.product:id,name',
                'items.orderItem.subOrder.vendor:id,store_name',
            ]);

            if (!empty($data['status'])) {
                $query->where('status', $data['status']);
            }

            if (!empty($data['search'])) {
                $search = $data['search'];
                $query->where(function ($q) use ($search) {
                    $q->where('return_number', 'like', "%{$search}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"));
                });
            }

            $returns = $query->latest()->paginate($data['per_page'] ?? 15);

            return response()->json([
                'success' => true,
                'message' => 'Disputes and return cases retrieved successfully.',
                'data' => $returns,
            ]);
        });
    }

    /**
     * Show single dispute case.
     */
    public function show(int $id)
    {
        return $this->execute(function () use ($id) {
            $dispute = ReturnModel::with([
                'user:id,name,email,phone',
                'order.subOrders.vendor:id,store_name',
                'items.orderItem.product.images',
                'items.orderItem.subOrder',
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Dispute details retrieved successfully.',
                'data' => $dispute,
            ]);
        });
    }

    /**
     * Resolve dispute in FAVOR OF CUSTOMER:
     * 1. Approves return request.
     * 2. Creates Refund record for the customer.
     * 3. Creates a REVERSAL LEDGER ENTRY (Debit) on the vendor's ledger.
     */
    public function resolveCustomer(Request $request, int $id)
    {
        return $this->execute(function () use ($request, $id) {
            $data = $request->validate([
                'admin_note' => ['required', 'string', 'min:5', 'max:500'],
                'refund_amount' => ['nullable', 'numeric', 'min:0'],
            ]);

            return DB::transaction(function () use ($id, $data) {
                $returnCase = ReturnModel::with('items.orderItem.subOrder')->lockForUpdate()->findOrFail($id);

                if (in_array($returnCase->status, ['completed', 'rejected'])) {
                    throw ValidationException::withMessages([
                        'status' => ["Case cannot be resolved because it is already {$returnCase->status}."],
                    ]);
                }

                // Compute total refund amount from return items if not explicitly specified
                $refundAmount = $data['refund_amount'] ?? 0;
                if ($refundAmount <= 0) {
                    foreach ($returnCase->items as $item) {
                        $refundAmount += $item->orderItem ? $item->orderItem->total_amount : 0;
                    }
                }

                $returnCase->update([
                    'status' => 'approved',
                    'admin_note' => $data['admin_note'],
                    'approved_at' => Carbon::now(),
                ]);

                // 1. Record customer refund
                $refund = Refund::create([
                    'refund_number' => 'REF-' . strtoupper(uniqid()),
                    'order_id' => $returnCase->order_id,
                    'user_id' => $returnCase->user_id,
                    'amount' => $refundAmount,
                    'status' => 'completed',
                    'reason' => 'Admin resolved return dispute in favor of customer: ' . $data['admin_note'],
                    'processed_at' => Carbon::now(),
                ]);

                // 2. Post Reversal Ledger Entry for affected vendor(s)
                foreach ($returnCase->items as $item) {
                    $subOrder = $item->orderItem?->subOrder;
                    if ($subOrder && $subOrder->vendor_id) {
                        $itemAmount = $item->orderItem->total_amount;
                        $lastLedger = VendorLedger::where('vendor_id', $subOrder->vendor_id)->latest()->first();
                        $currentBal = $lastLedger ? $lastLedger->balance_after : 0;
                        $newBal = $currentBal - $itemAmount;

                        VendorLedger::create([
                            'vendor_id' => $subOrder->vendor_id,
                            'reference_type' => 'App\Models\ReturnModel',
                            'reference_id' => $returnCase->id,
                            'transaction_type' => 'debit',
                            'amount' => $itemAmount,
                            'balance_after' => $newBal,
                            'status' => 'completed',
                            'remarks' => "Reversal debit: Dispute #{$returnCase->return_number} resolved in customer favor.",
                            'transaction_date' => Carbon::now(),
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Dispute resolved in favor of customer. Refund initiated and vendor reversal ledger created.',
                    'data' => [
                        'return' => $returnCase,
                        'refund' => $refund,
                    ],
                ]);
            });
        });
    }

    /**
     * Resolve dispute in FAVOR OF VENDOR:
     * 1. Rejects customer return.
     * 2. Notifies customer with reason.
     * 3. Releases vendor funds to eligible/payable ledger.
     */
    public function resolveVendor(Request $request, int $id)
    {
        return $this->execute(function () use ($request, $id) {
            $data = $request->validate([
                'admin_note' => ['required', 'string', 'min:5', 'max:500'],
            ]);

            return DB::transaction(function () use ($id, $data) {
                $returnCase = ReturnModel::lockForUpdate()->findOrFail($id);

                if (in_array($returnCase->status, ['completed', 'rejected'])) {
                    throw ValidationException::withMessages([
                        'status' => ["Case cannot be resolved because it is already {$returnCase->status}."],
                    ]);
                }

                $returnCase->update([
                    'status' => 'rejected',
                    'admin_note' => $data['admin_note'],
                    'rejected_at' => Carbon::now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Dispute resolved in favor of vendor. Return claim rejected and vendor ledger released.',
                    'data' => $returnCase,
                ]);
            });
        });
    }
}
