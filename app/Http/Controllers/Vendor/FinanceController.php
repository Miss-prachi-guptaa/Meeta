<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorLedger;
use App\Models\VendorPayout;
use App\Models\VendorSettlement;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Vendor financial summary & balance overview.
     */
    public function overview(Request $request)
    {
        return $this->execute(function () use ($request) {
            $vendor = $this->vendor($request);

            // Latest balance from ledger
            $latestLedger = VendorLedger::where('vendor_id', $vendor->id)->latest()->first();
            $currentBalance = $latestLedger ? $latestLedger->balance_after : 0;

            // Settlements breakdown
            $pendingSettlements = VendorSettlement::where('vendor_id', $vendor->id)->where('status', 'pending')->sum('net_amount');
            $settledEarnings = VendorSettlement::where('vendor_id', $vendor->id)->where('status', 'settled')->sum('net_amount');
            $totalDisbursed = VendorPayout::where('vendor_id', $vendor->id)->where('status', 'completed')->sum('amount');

            return response()->json([
                'success' => true,
                'message' => 'Vendor finance overview retrieved successfully.',
                'data' => [
                    'current_balance' => (float) $currentBalance,
                    'pending_clearance' => (float) $pendingSettlements,
                    'settled_earnings' => (float) $settledEarnings,
                    'total_payouts' => (float) $totalDisbursed,
                ],
            ]);
        });
    }

    /**
     * Detailed ledger transactions log (Credits from delivered orders & Debits from payouts/refunds).
     */
    public function ledger(Request $request)
    {
        return $this->execute(function () use ($request) {
            $vendor = $this->vendor($request);
            $perPage = $request->input('per_page', 15);

            $transactions = VendorLedger::where('vendor_id', $vendor->id)
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vendor ledger transactions retrieved successfully.',
                'data' => $transactions,
            ]);
        });
    }

    /**
     * History of payouts received from Admin batches.
     */
    public function payouts(Request $request)
    {
        return $this->execute(function () use ($request) {
            $vendor = $this->vendor($request);
            $perPage = $request->input('per_page', 15);

            $payouts = VendorPayout::where('vendor_id', $vendor->id)
                ->latest()
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Vendor payout history retrieved successfully.',
                'data' => $payouts,
            ]);
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
